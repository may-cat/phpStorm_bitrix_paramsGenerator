#!/bin/sh

#
# Вычленение локализации
bx_localize_parse(){
    echo "LOCALIZE"
    /z/usr/bin/php.exe -q ~/consoleJedi/scripts/extract.php `pwd`
}
