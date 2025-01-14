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

xml_dir = "C:\\Users\\girar\\Documents\\1. Cours\\Projet Professionnel - ARGILES\\transcriptions normalisées ARGILES COMONIMAGE\\"

while not os.path.isdir(xml_dir):
    xml_dir = input("Veuillez donner le lien vers le dossier des fichiers xml")

files = [f for f in os.listdir(xml_dir) if os.path.isfile(os.path.join(xml_dir, f))]
document = {}
groupes = {}
audites = {}

for dir in files:
    with open(os.path.join(xml_dir,dir),mode='r',encoding='utf-8') as f:
        lines = f.readlines()

        for i,line in enumerate(lines):
            line = line.strip()
            #print(line)
            try:

                if line[:6] == "<etude":    # <etude idEtude=x sujet=x groupeTemoins=x>
                    etude_id = line.split('idEtude="')[1].split('"')[0].strip()
                    etude_sujet = line.split('sujet="')[1].split('"')[0].strip()
                    etude_groupeTemoins = line.split('groupeTemoins="')[1].split('"')[0].strip()
                    if sujet in document.keys():
                        pass
                    elif sujet != '':
                        document[etude_sujet] = {"questions":{},
                            "etude_id":etude_id,
                            "groupes":[]
                            }

                if line[:9] == "<question": # <question idQuestion=x texteQuestion=x type=x>
                    question_id = line.split('idQuestion="')[1].split('"')[0].strip()   # Unused
                    question_enonce = line.split('texteQuestion="')[1].split('"')[0].strip()
                    question_type = line.split('type="')[1].split('"')[0].strip()
                    if question_type not in document[etude_sujet]["questions"].keys():
                        document[etude_sujet]["questions"]["question_type"] = question_enonce

                if line[:7] == "<groupe":   # <groupe idGroupe=x nomGroupe=x>
                    groupe_id = line.split('idGroupe="')[1].split('"')[0].strip()
                    groupe_nom = line.split('nomGroupe="')[1].split('"')[0].strip()

                    if groupe_nom not in document[etude_sujet]["groupes"]:
                        document[etude_sujet]["groupes"].append(groupe_nom)
                        groupes[groupe_nom] = {"etude":etude_sujet,
                            "groupe_id":groupe_id,
                            "audites":[]
                            }
                        
                if line[:8] == "<reponse":  # <reponse idAudite=x>
                    id_audite = line.split('idAudite="')[1].split('"')[0].strip()
                    if id_audite not in groupes[groupe_nom]["audites"]:
                        groupes[groupe_nom]["audites"].append(id_audite)
                        audites[id_audite] = {
                            "groupe":groupe_nom,
                            "reponses":{}
                            }
                
                if line[:5] == "<idee":     # <idee idIdee=X>XX</idee>
                    idee_id = line.split('idIdee="')[1].split('"')[0].strip()
                    idee_line = line.split('>')[1].split('<')[0].strip()
                    if question_type in audites[id_audite]["reponses"].keys():
                        audites[id_audite]["reponses"][question_type].append(idee_line)
                    else:
                        audites[id_audite]["reponses"][question_type] = [idee_line]

            except ValueError as val:
                print("Value Error : ",val)
"""
            except Exception as error:
                print("Erreur ligne ",i," : ",line," : ",error)
"""


                


# ---------- Fin du script -----------

for x in [document,groupes,audites]:
    for key in x.keys():
        print(key, x[key])
print(files)
print('Terminé')