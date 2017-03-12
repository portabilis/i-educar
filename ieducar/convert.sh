for file in *.php; do
   echo "Processing $file file..."
   iconv -f UTF-8 -t utf-8 "$file" -o "$file"
done
