""" FR
Extracteur d'informations créé pour le projet ARGILES
Plus d'informations sur https://github.com/zancap/ARGILES-Database-Management-and-NLP

Auteur : Romain Girard
"""

""" ENG
Info extractor made for the ARGILES project.
More information on https://github.com/zancap/ARGILES-Database-Management-and-NLP

Author : Romain Girard
"""

import os,shelve
import string
import xml.etree.ElementTree as ET
import nltk
from nltk.corpus import stopwords

# ----------- Fonctions de support -----------

def tokenize(text:str):
    punctuation = " -'"+string.punctuation
    letters = string.ascii_letters + 'àâéèêìòôùûç'
    numbers = string.digits
    output = []
    token = []
    last_char = ''
    for char in text:
        if char in punctuation and last_char in letters+numbers and last_char != '':
            if char == "'":
                token.append(char)
            output.append(''.join(token).strip())
            token = []
        else:
            token.append(char)
        last_char = char

    to_print = False
    for idee in output:
        if ' ' in idee:
            to_print = True
    
    if to_print:
        print(output)

    return output

# ----------- Fonctions -----------

def read_xmls(xml_dir = "../xmls/"):
    """
        input : Dossier contenant les fichiers xml à lire

        Lit les fichiers xml et récupère les informations si le script est bien organisé selon la DTD (Document > Question > Groupe > Reponse > Idee)
        Renvoie 3 dictionnaires :
            Document ['name'] = {id:int, questions: list(str), groupes:list(str) KEY-DOWN}
            Groupes ['name'] = {id:int, document: str KEY-UP, audites:list(str) KEY-DOWN}
            Audites ['id'] = {groupe:str KEY-UP, reponses : dict {type:str->reponse:str}}
            Chaque dictionnaire contient une clé pour passer au dictionnaire supérieur (KEY-UP) / inférieur (KEY-DOWN)
    """
    
    if not os.path.isdir(xml_dir):
        print(f"Ce script est situe a {os.getcwd()}, et {xml_dir} n'est pas le lien vers les fichiers xmls.")

    files = [f for f in os.listdir(xml_dir) if os.path.isfile(os.path.join(xml_dir, f))]
    document = {}
    groupes = {}
    audites = {}

    try:
        for dir in files:
            with open(os.path.join(xml_dir,dir),mode='r',encoding='utf-8') as f:
                xml_text = f.read()
                root = ET.fromstring(xml_text)

                # 1. Extraction of document informations
                
                ongoing_text = root.attrib['sujet']

                if ongoing_text not in document.keys():
                    document[ongoing_text] = {
                        "id":root.attrib['idEtude'],
                        "questions":{},
                        "groupes":[]
                    }
                else:
                    raise KeyError("Document déjà existant")


                # 2. Extraction of questions informations
                for question in root:
                    ongoing_question_type = question.attrib['type']
                    document[ongoing_text]["questions"][ongoing_question_type] = {"texte":question.attrib['texteQuestion'],"id":question.attrib['idQuestion']}

                    for groupe in question:
                        ongoing_group = groupe.attrib['nomGroupe']
                        if ongoing_group not in groupes.keys():
                            groupes[ongoing_group] = {
                                'id':groupe.attrib['idGroupe'],
                                "document":ongoing_text,
                                "audites":[]
                            }

                        if ongoing_group not in document[ongoing_text]['groupes']:
                            document[ongoing_text]['groupes'].append(ongoing_group)

                        for reponse in groupe:
                            ongoing_audite = reponse.attrib['idAudite']
                            if ongoing_audite not in audites.keys():
                                audites[ongoing_audite] = {
                                    "groupe" : ongoing_group,
                                    "reponses": {
                                    }
                                }
                            
                            if ongoing_text not in audites[ongoing_audite]["reponses"].keys():
                                audites[ongoing_audite]["reponses"][ongoing_text] = {}

                            for idee in reponse:
                                if ongoing_question_type in audites[ongoing_audite]["reponses"][ongoing_text].keys():
                                    audites[ongoing_audite]["reponses"][ongoing_text][ongoing_question_type].append(idee.text)
                                else:
                                    audites[ongoing_audite]["reponses"][ongoing_text][ongoing_question_type] = [idee.text]

        return document,groupes,audites

    except KeyError as err:
        print("Erreur : ",err)
        raise
    
def stats(ids_audites):
    """
        Extraction de statistiques pour un ensemble d'audités.
        Informations extraites :
            - Mots plus présents (10 plus présents)
            - Valence des réponses (positive / négative) [A VENIR]

        input : [id_document, [liste d'identifiants audités] ]

        output : liste []
    """
    nltk.download('stopwords')
    stoplist = stopwords.words('french')
    document,groupes,audites = read_xmls()
    ongoing_doc = ids_audites[0]
    questions = {}
    dict = {}

    for id in ids_audites[1]:
        if str(id) in audites.keys() and ongoing_doc in audites[str(id)]['reponses'].keys():
            for reponse in audites[str(id)]['reponses'][ongoing_doc]:
                if reponse not in questions.keys():
                    questions[reponse] = []
                for idee in audites[str(id)]['reponses'][ongoing_doc][reponse]:
                    questions[reponse].append(idee)
                    for token in tokenize(idee):
                        if token.lower() not in stoplist and token[:-1].lower() not in stoplist:
                            if token in dict.keys():
                                dict[token] +=1
                            else:
                                dict[token] = 1
    
    for cat in questions.keys():
        questions[cat]
    #print(sorted([[x,dict[x]] for x in dict.keys()],key=lambda x:x[1],reverse=True))
    pass

def get_audite(id_audite:str,manga:str,xml_dir='./xmls/'):
    """
        Récupération des réponses d'un audité spécifique depuis le xml, pour affichage web.
    """
    document,groupes,audites = read_xmls(xml_dir)
    output = []
    if manga in audites[id_audite]["reponses"].keys():
        for question in audites[id_audite]['reponses'][manga].keys():
            text_reponses = []
            for idea in audites[id_audite]['reponses'][manga][question]:
                text_reponses.append(' '.join(audites[id_audite]['reponses'][manga][question]))
            output.append(question+' : '+' '.join(text_reponses))
        return ' | '.join(output)


# ---------- Début du script -----------

#document,groupes,audites = read_xmls()
#print(audites)
#print(get_audite('107'))
#print(stats(["manga Roméo et Juliette",range(100,200)]))
                


# ---------- Fin du script -----------

#print('Terminé')