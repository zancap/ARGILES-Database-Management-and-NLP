from extraction_info import *
import sys
import os

# Récupération des arguments de la ligne de commande
args = sys.argv
args = [x.strip() for x in args]    # Nettoyage des arguments

# Extraction de données de l'xml
if len(args) >= 4:
    xml_dir = args[3]
else:
    xml_dir = './xmls/'

document,groupes,audites = read_xmls(xml_dir)

# Récupération des infos pour l'ID

if len(args) >= 3 and args[1] in audites.keys():
    if args[2] in audites[args[1]]["reponses"].keys():
        output = get_audite(args[1],args[2],xml_dir)
        print(output)
    else:
        print("Incorrect document\nPossibles documents :")
        for key in audites[args[1]]["reponses"].keys():
            print('\t',key)
else:
    print("Unknown ID")