# STOP
Inden du benytter dette script skal du være opmærksom på at alt sker på eget ansvar. Dette er et script vi selv har brugt til at eksportere vores domæner. Læs hvordan det virker så du ved hvorfor det holder op med at virke, hvis GDNS ændrer i deres opsætning.
UANSET hvad så er det på eget ansvar. Jeg har ikke tjekket om det bryder med nogen terms and conditions hos GratisDNS. Igen. På eget ansvar!!

# gratisdns-exporter - WHY

GratisDNS stopper deres service d. 1/3-2022 efter at være blevet opkøbt af One.com
One.com har sagt at alle får et gratis dns abonnement. Det er dog ikke noget alle kan bruge.
Der er ikke umiddelbart meldt noget ud om Templates. Derfor har vi været nødt til at finde en anden udbyder der understøtter templates.

Denne udbyder har vi ikke fundet og derfor udviklet vores egen OctoDNS-lign. service med baseret på MySQL i stedet for flade filer.

Vi har pænt mange domæner og har været glade for deres templating.

# Configuration
Rediger filen login.sh og udskift XXXXXXX med username og YYYYYYY med password

# Environment / requirements
Script er kun kørt på en debian maskine og kræver at både curl og php er installeret.

# How it works
## run.sh:
kørsel af komplet script

### login.sh:
Script starter med at skabe en session vha curl. Tak til https://github.com/zylopfa/acme.sh_dns_gratisdns/blob/master/dns_gratisdns.sh for hints. 'cookiefile' sættes. (Har ikke talt med vedkommende blot kigget kode)

### domain_list.sh:
Script downloader en html side fra GDNS som viser listen over alle domæner og templates. Herunder selvstændige domæner. Laver en midlertidig fil 'gratisdns.html'. benytter 'cookiefile'

### php extract_links.php:
Tager 'gratisdns.html' ind og finder alle links, hvor "knappen" hedder "eksport", går baglæns i Nodes indtil den rammer tr og henter navnet på domænet/templaten.
Hvis der står (template) i navnet antager vi det er en template (hvis jeres navngivning indeholder template må du selv lige tilpasse script.)

Hvis ikke der står template, kigger vi på hvorvidt domænetnavnet starter med /[a-zA-Z0-9]/ og ellers antager vi det er "under seneste template". Alle domæner der ligger under templates starter med "-".

Til sidst skaber vi et download script som benytter vores 'cookiefile' og curl til at downloade og placere filerne i en export_from_gdns mappe.
Strukturen i mappen er som følger
````
export_from_gdns/templatenavnUdenMellemRumEllerSpecialtegn/0 #første domæne
export_from_gdns/templatenavnUdenMellemRumEllerSpecialtegn/1 #andet domæne
export_from_gdns/templatenavnUdenMellemRumEllerSpecialtegn/2 #tredje domæne
export_from_gdns/endnuEnTemplatenavnUdenMellemRumEllerSpecialtegn/0 #første domæne
export_from_gdns/endnuEnTemplatenavnUdenMellemRumEllerSpecialtegn/1 #andet domæne
export_from_gdns/endnuEnTemplatenavnUdenMellemRumEllerSpecialtegn/2 #tredje domæne
export_from_gdns/domæne.dk/domæne.dk
````
### download.sh:
Filen eksisterer midlertidigt og er dannet af extract_links.php for at foretage download af de enkelte zoner.

## tilbage til run.sh:
cleanup. Fjerner download.sh, cookiefile,gratisdns.html

# Quirks
Nogle domæner har "forkerte" zonefiler. Disse zonefiler virker som om at de ikke bliver indlæst. Jeg har kun oplevet disse på enkelte domæner. I mit script ser jeg blot om der er en "$" i zone-filen hvorefter jeg antager den er broken og skal indlæses manuelt fra deres side.

Min bash kundskaber er ikke de store. Derfor må du nøjes med at ændre direkte i login.sh filen. mappenavnene på templates er lavet så jeg ikke skal tænke over om filsystem understøtter
