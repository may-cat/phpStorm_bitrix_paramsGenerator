#!/bin/sh

#
# Формируем файл параметров
## Предполагается, что мы уже в папке компонента
bx_params_parse(){
    if [ -f ".parameters.php" ]
    then
        echo "Удалите существующий файл .parameters.php"
        return 1
    fi

    # Добываем список параметров
    params=( $( "C:\Program Files (x86)\GnuWin32\bin\grep.exe" -ohPr  "(?<=arParams\[)(.*)(?=\])" . ) )
    # Оставляем из них только уникальные
    unique=( `echo ${params[@]} | tr ' ' '\n' | "C:\Program Files (x86)\Git\bin\sort.exe" -u` )
    # И начинаем формировать файл
    cat ~/consoleJedi/data/component-params/beginning.txt > ".parameters.php"
    for i in "${unique[@]}"
    do
        str=$( sed s/#CODE#/$i/ <~/consoleJedi/data/component-params/body.txt )
        echo $str >> ".parameters.php"
    done
    cat ~/consoleJedi/data/component-params/end.txt >> ".parameters.php"
}

