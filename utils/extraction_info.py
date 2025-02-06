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
import xml.etree.ElementTree as ET

# ----------- Fonctions -----------

def read_xmls(xml_dir = "xmls/"):
    """
        input : Dossier contenant les fichiers xml à lire

        Lit les fichiers xml et récupère les informations si le script est bien organisé selon la DTD (Document > Question > Groupe > Reponse > Idee)
        Renvoie 3 dictionnaires :
            Document ['name'] = {id:int, questions: list(str), groupes:list(str) KEY-DOWN}
            Groupes ['name'] = {id:int, document: str KEY-UP, audites:list(str) KEY-DOWN}
            Audites ['id'] = {groupe:str KEY-UP, reponses : dict {type:str->reponse:str}}
            Chaque dictionnaire contient une clé pour passer au dictionnaire supérieur (KEY-UP) / inférieur (KEY-DOWN)
    """
    while not os.path.isdir(xml_dir):
        xml_dir = input("Veuillez donner le lien vers le dossier des fichiers xml")

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
    pass

def get_audite(id_audite:str):
    document,groupes,audites = read_xmls()
    output = []
    for text in audites[id_audite]['reponses'].keys():
        text_reponses = []
        for key in audites[id_audite]['reponses'][text].keys():
            text_reponses.append(' '.join(audites[id_audite]['reponses'][text][key]))
        output.append([text,text_reponses])
    return output


# ---------- Début du script -----------

document,groupes,audites = read_xmls()
#print(audites)
print(get_audite('107'))
                


# ---------- Fin du script -----------

print('Terminé')
