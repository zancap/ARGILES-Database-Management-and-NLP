import sys,os

xml_dir = "./XMLs/" # Changer ici le répertoire des documents
xml_docs = [f for f in os.listdir(xml_dir) if os.path.isfile(os.path.join(xml_dir, f))]
stats = {
}

reponse = []

for f in xml_docs:
    stats[f] = {
        "evocation" : {
            "nb_char" : [],
            "nb_idees" : []
            },
        "like" : {
            "nb_char" : [],
            "nb_idees" : []
            },
        "dislike" : {
            "nb_char" : [],
            "nb_idees" : []
            },
        "expectation" : {
            "nb_char" : [],
            "nb_idees" : []
            }
        }
    with open(os.path.join(xml_dir,f),mode='r',encoding="utf-8") as file:
        for line in file.readlines():
            line = line.strip()

            if line[:9] == '<question':
                question_type = line.split('type="')[1].split('"')[0].strip()

            if line == '</reponse>':
                stats[f][question_type]["nb_char"].append(len("".join(reponse)))
                stats[f][question_type]["nb_idees"].append(len(reponse))
                #print(question_type," : ",reponse)
                reponse = []

            if line[:5] == '<idee':
                reponse.append(line.split('>')[1].split('<')[0].strip())


stats_precised = {}

# Type of question : [ [min_length,med_length,moy_length,max_length], [min_idees,med_idees,moy_idees,max_idees] ]

for f in stats.keys():
    stats_precised[f] = {
        "evocation": [],
        "like":[],
        "dislike":[],
        "expectation":[]
        }

    for type_question in stats_precised[f].keys():
        stats_precised[f][type_question] = []
        for x in ['nb_char','nb_idees']:
            output = []
            output.append(min(stats[f][type_question][x]))
            
            medium_val = len(stats[f][type_question][x])/2
            if medium_val%1 != 0:   # Si le nombre (len(nb_char/idees)) est impair
                medium_val = int(medium_val + 0.5)
            else:
                medium_val = int(medium_val)

            output.append(stats[f][type_question][x][medium_val])
            output.append(sum(stats[f][type_question][x])/len(stats[f][type_question][x]))
            output.append(max(stats[f][type_question][x]))

            stats_precised[f][type_question].append(output)


for i,doc in enumerate(stats_precised.keys()):
    print("\n  ----- ----- ----- ----- -----\n")
    print(f"Document {i+1} : {doc}")
    for type_question in stats_precised[doc]:
        print(f" ----- {type_question} -----")
        for type_val in range(2):
            type_val = type_val-1
            if type_val == 0:
                print("\tNombre de caractères :")
            else:
                print("\tNombre d'idées :")                
            print(f"\t\tMinimum : {stats_precised[doc][type_question][type_val][0]}")
            print(f"\t\tValeur médiane : {stats_precised[doc][type_question][type_val][1]}")
            print(f"\t\tMoyenne : {stats_precised[doc][type_question][type_val][2]}")
            print(f"\t\tMaximum : {stats_precised[doc][type_question][type_val][3]}")





                                
