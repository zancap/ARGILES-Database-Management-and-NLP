"""
Extraction d'informations des réponses des étudiants sur un texte donné
Arguments :
    - (str) document
    - (int) ID de l'élève 1
    - (int) ID de l'élève 2
    ...

    Les arguments à donner sont à regrouper pour un même document, ex :
    "roman Sirius" 101 102 104 106 110 111 "manga Roméo et Juliette" 101 103 104 105 106 112
"""

from extraction_info import *
import sys
import os

# Récupération des arguments de la ligne de commande
args = sys.argv
args = [x.strip() for x in args]    # Nettoyage des arguments

# Extraction de données de l'xml
if '/' in args[-1]:
    xml_dir = args[-1]
else:
    xml_dir = './xmls/'

document,groupes,audites = read_xmls(xml_dir)

# Récupération des statistiques

infos = []
bloc = ['',[]]
for x in args:
    if x[0] in string.ascii_letters:    # nom du document
        if bloc != ['',[]]:
            infos.append(bloc)
            bloc = ['',[]]
        bloc[0] = x

    elif x[0] in string.digits:         # ID d'un élève
        bloc[1].append(x)

print(infos)
if len(ids) > 0:
    for bloc in infos:
        stats(bloc)
else:
    print("Unknown IDs")