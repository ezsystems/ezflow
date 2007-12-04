#!/bin/sh

function show_help
{
    echo
    echo "Make package."
    echo
    echo "Usage: $0 [options]"
    echo
    echo "Options: -h"
    echo "         --help                This message."
    echo "         --output-dir=<dir>    Where to put packages."
    echo
}

function parse_cl_parameters
{
    for arg in $*; do
        case $arg in
        --help|-h)
            show_help
            exit 1
            ;;

        --output-dir*)
            if echo $arg | grep -e "--output-dir=" >/dev/null; then
                OUTPUT_DIR=`echo $arg | sed 's/--output-dir=//'`
            fi
            ;;

        --*)
            if [ $? -ne 0 ]; then
                echo "$arg: unknown long option specified"
                echo
                echo "Type '$0 --help\` for a list of options to use."
                exit 1
            fi
            ;;
        -*)
            if [ $? -ne 0 ]; then
                echo "$arg: unknown option specified"
                echo
                echo "Type '$0 --help\` for a list of options to use."
                exit 1
            fi
            ;;
        esac;
    done
}


## main ################################################

# "Declare" all variables used in the script.
OUTPUT_DIR='/tmp/ezflow/packages'
EXCLUDE_FILES="--exclude .svn --exclude .cache --exclude pack_and_publish.bat"

# Do the work.
parse_cl_parameters $*

if [ ! -d "$OUTPUT_DIR" ];then
    echo "Creating output dir: '$OUTPUT_DIR'"
    mkdir -p "$OUTPUT_DIR"
else
    echo "Output dir: '$OUTPUT_DIR'"
fi

cd packages

for dir in *; do
    if [ -d "$dir" -a ! "$dir" = "package_set" ]; then
        echo "Packing $dir.ezpkg";
        rm -rf "$OUTPUT_DIR/$dir.ezpkg"
        (cd $dir && tar -cz $EXCLUDE_FILES -f "$OUTPUT_DIR/$dir.ezpkg" *)
    fi
done
