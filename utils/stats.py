"""
Extraction d'informations des réponses des étudiants sur un texte donné
Arguments :
    - (str) document
    - (int) ID de l'élève 1
    - (int) ID de l'élève 2
    ...

    Les arguments à donner sont les documents et groupes du filtre, ex :
    "roman Sirius" "manga Roméo et Juliette" "CM2" ['../xmls/']
"""

from extraction_info import *
import sys
import os

# Récupération des arguments de la ligne de commande
args = sys.argv
args = [x.strip() for x in args]    # Nettoyage des arguments

# Extraction de données de l'xml
stats(args[1:])

