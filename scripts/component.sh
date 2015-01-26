#!/bin/sh
# Настройки для меня
folder=${PWD##*/}
curpath=${PWD}
lb=$'\n'

source ~/phpStorm_bitrix_paramsGenerator/scripts/component_repair.sh
source ~/phpStorm_bitrix_paramsGenerator/scripts/component_loc.sh
source ~/phpStorm_bitrix_paramsGenerator/scripts/component_params.sh

#
# Функция перехода к корневой папке
## Просто переходим в папку
bx_rt(){
 cd $curpath/
 cd $folder/
}

#
# Создаём компонент
## Переходим в папку компонента, при необходимости создаём папку
bx_component_folder(){
    bx_rt

    # Входим в папку
    if [ -d local/ ]
    then
        cd local/
    else
        cd bitrix/
    fi

    # Входим в папку компонентов
    cd components

    # Создаём пространство
    if [ ! -d $1 ]
    then
        mkdir $1
    fi
    cd $1

    # Создаём компонент
    if [ ! -d $2 ]
    then
        mkdir $2
    fi
    cd $2
}



#
# Функция перехода к компоненту
c(){
    bx_rt

    # Разбираем переданный параметр
    IFS=':' read -a arComponent <<< "$1"

    # Переходим в папку компонента компонента
    if [ -d local/components/${arComponent[0]}/${arComponent[1]}/ ];
    then
        # Компонент существует в папке /local/
        cd local/components/${arComponent[0]}/${arComponent[1]}/
    else
        if [ -d local/components/${arComponent[0]}/${arComponent[1]}/ ];
        then
            # Компонент существует в папке /bitrix/
            cd bitrix/components/${arComponent[0]}/${arComponent[1]}/
        else
            # Компонент не найден, может быть создать его надо?
            while true; do
                read -r -p "Папки компонента нет, создать? (Y/N) " REPLY
                case $REPLY in
                    [Yy]* ) bx_component_folder ${arComponent[0]} ${arComponent[1]}; break;;
                    [Nn]* ) return;;
                    * ) echo "Please answer yes or no.";;
                esac
            done
        fi
    fi

    # Дальше по идее нужно разбираем разные режимы
    case $2 in
        [params]* ) bx_params_parse ${arComponent[0]} ${arComponent[1]};;
        [loc]* ) bx_localize_parse ${arComponent[0]} ${arComponent[1]};;
        * ) bx_component_repair ${arComponent[0]} ${arComponent[1]};;
    esac
}
