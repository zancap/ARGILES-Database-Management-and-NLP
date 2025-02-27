from extraction_info import *
import sys
import os

# Récupération des arguments de la ligne de commande
args = sys.argv
arguments = [x.replace('\udcc3\udca9','é') if '\udcc3\udca9' in x else x.strip() for x in args]    # Nettoyage des arguments

# Extraction de données de l'xml
if len(arguments) >= 4:
    xml_dir = arguments[3]
else:
    xml_dir = './xmls/'

document,groupes,audites = read_xmls(xml_dir)

# Récupération des infos pour l'ID

if len(arguments) >= 3 and arguments[1] in audites.keys():
    if arguments[2] in audites[arguments[1]]["reponses"].keys():
        output = get_audite(arguments[1],arguments[2],xml_dir)
        print(output)
    else:
        print("Document incorrect : '",arguments[2],"'\nPossibles documents :")
        for key in audites[arguments[1]]["reponses"].keys():
            print('\t',key)
else:
    print("Unknown ID")