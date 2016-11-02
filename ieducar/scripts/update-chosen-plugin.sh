echo "Updating chosen plugin..."

cd ~/projects/i*educar/trunk/modules/Portabilis/Assets/Plugins

rm -r Chosen
mkdir Chosen

cd Chosen
wget https://raw.github.com/harvesthq/chosen/master/LICENSE.md
wget https://raw.github.com/harvesthq/chosen/master/chosen/chosen.jquery.min.js
wget https://raw.github.com/harvesthq/chosen/master/chosen/chosen.css
wget https://raw.github.com/harvesthq/chosen/master/chosen/chosen-sprite@2x.png
wget https://raw.github.com/harvesthq/chosen/master/chosen/chosen-sprite.png

echo "\nDone, now you must update the assets version!"