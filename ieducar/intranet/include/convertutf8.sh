for file in *.php; do
   echo "Processing $file file..."
   iconv -f iso-8859-1 -t utf-8 "$file" -o "$file"
done
