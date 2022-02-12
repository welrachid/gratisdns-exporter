./login.sh
./domain_list.sh
php extract_links.php
#extract_links creates a download.sh file that will download the exports.
./download.sh
#Import script can be written here.

#php import.php

#cleanup
rm gratisdns.html
rm download.sh
rm cookiefile
