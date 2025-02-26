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
            output.append(''.join(token).strip(' ,;:"'))
            token = []
        else:
            token.append(char)
        last_char = char

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
                            if ongoing_audite not in groupes[ongoing_group]['audites']:
                                groupes[ongoing_group]['audites'].append(ongoing_audite)
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

def to_string(text):
    try:
        return str(text)
    except Exception:
        return ""
    
def dl_stopwords():
    """
        Télécharge la liste des stopwords depuis nltk, si elle n'est pas disponible
    """
    
    try:
        nltk.data.find('/var/www/nltk_data/corpora/stopwords.zip')
    except LookupError:   
        nltk.download('stopwords')                  # Liste de stopwords

def stats(*args):
    """
        Extraction de statistiques pour un ensemble d'audités.
        Informations extraites :
            - Mots plus présents (10 plus présents)
            - Valence des réponses (positive / négative) [A VENIR]

        input : [id_document,[id_document, id_group] ]

        output : string
    """

    dl_stopwords()
    
    if '/' in args[-1][-1]:
        xml_directory = args[-1][-1]
    else:
        xml_directory = '../xmls/'
        
    stoplist = stopwords.words('french')
    stoplist_cat = {                            # Stopwords spécifique à la catégorie
        'like':['aimer','aimé'],
        'dislike':[],
        'expectation':[],
        'evocation':['évoquer','évoque']
    }

    document,groupes,audites = read_xmls(xml_directory)
    ongoing_doc = []
    ongoing_groups = []
    for arg in args[0]:
        if arg in document.keys():
            ongoing_doc.append(arg)
        elif arg in groupes.keys():
            ongoing_groups.append(arg)
            
    categories = []
    dict = {}
    for id_group in groupes.keys():
        if id_group in ongoing_groups or len(ongoing_groups) == 0:
            for id_audite in groupes[id_group]['audites']:
                for doc in audites[id_audite]['reponses'].keys():
                    if doc in ongoing_doc or len(ongoing_doc) == 0:
                        for categorie in audites[id_audite]['reponses'][doc].keys():
                            if categorie.lower() not in dict.keys():
                                dict[categorie.lower()] = {}
                            if categorie.lower() not in categories:
                                categories.append(categorie.lower())
                                
                            for idee in audites[id_audite]['reponses'][doc][categorie]:
                                traitement_nlp = tokenize(idee)
                                for token in traitement_nlp:
                                    token = token.strip(' ')
                                    if token.lower() not in stoplist and token.lower() not in stoplist_cat[categorie.lower()] :
                                        if token in dict[categorie].keys():
                                            dict[categorie][token] +=1
                                        else:
                                            dict[categorie][token] = 1
        
    
    dic1 = {}

    for cat in categories:
        dic1[cat] = sorted([[x,dict[cat][x]] for x in dict[cat].keys()],key=lambda x:x[1],reverse=True)
    
    nb_lignes = pow(10,10)
    for cat in dic1.keys():
        nb_lignes = min(nb_lignes,len(dic1[cat]))
    
    for i in range(len(categories)):
        print(categories[i],end='')
        for j in range(nb_lignes):
            if i < len(dic1):
                print ('|'+dic1[categories[i]][j][0]+'|'+str(dic1[categories[i]][j][1]),end='')
        print(';',end='')

def stats_lemma(*args):
    """
        Extraction de statistiques pour un ensemble d'audités.
        Utilise le lemmatiseur de spacy, ajout nécessaire au serveur :
            python -m spacy download fr_core_news_md
        Informations extraites :
            - Mots plus présents (10 plus présents)
            - Valence des réponses (positive / négative) [A VENIR]

        input : [id_document,[id_document, id_group] ]

        output : string
    """
    import spacy
    nlp = spacy.load('fr_core_news_md')         # Lemmatiseur par Spacy

    dl_stopwords()
    
    if '/' in args[-1][-1]:
        xml_directory = args[-1][-1]
    else:
        xml_directory = '../xmls/'
        
    stoplist = stopwords.words('french')
    stoplist_cat = {                            # Stopwords spécifique à la catégorie
        'like':['aimer','aimé'],
        'dislike':[],
        'expectation':[],
        'evocation':['évoquer','évoque']
    }

    document,groupes,audites = read_xmls(xml_directory)
    ongoing_doc = []
    ongoing_groups = []
    for arg in args[0]:
        if arg in document.keys():
            ongoing_doc.append(arg)
        elif arg in groupes.keys():
            ongoing_groups.append(arg)
            
    categories = []
    dict = {}
    for id_group in groupes.keys():
        if id_group in ongoing_groups or len(ongoing_groups) == 0:
            for id_audite in groupes[id_group]['audites']:
                for doc in audites[id_audite]['reponses'].keys():
                    if doc in ongoing_doc or len(ongoing_doc) == 0:
                        for categorie in audites[id_audite]['reponses'][doc].keys():
                            if categorie not in dict.keys():
                                dict[categorie] = {}
                            if categorie not in categories:
                                categories.append(categorie)
                                
                            for idee in audites[id_audite]['reponses'][doc][categorie]:
                                doc = nlp(idee)
                                for tok in doc:
                                    lemma = tok.lemma_
                                    if lemma.lower() not in stoplist and lemma.lower() not in stoplist_cat[categorie] :
                                        if lemma in dict[categorie].keys():
                                            dict[categorie][lemma] +=1
                                        else:
                                            dict[categorie][lemma] = 1
        
    
    dic1 = {}

    for cat in categories:
        dic1[cat] = sorted([[x,dict[cat][x]] for x in dict[cat].keys()],key=lambda x:x[1],reverse=True)
    
    nb_lignes = pow(10,10)
    for cat in dic1.keys():
        nb_lignes = min(nb_lignes,len(dic1[cat]))
    
    for i in range(len(categories)):
        print(categories[i],end='')
        for j in range(nb_lignes):
            if i < len(dic1):
                print ('|'+dic1[categories[i]][j][0]+'|'+str(dic1[categories[i]][j][1]),end='')
        print(';',end='')

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