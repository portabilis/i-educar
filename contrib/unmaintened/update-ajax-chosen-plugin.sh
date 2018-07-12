echo "Updating ajax-chosen plugin..."

cd ~/projects/i*educar/trunk/modules/Portabilis/Assets/Plugins

rm -r AjaxChosen
mkdir AjaxChosen

cd AjaxChosen
wget https://raw.github.com/meltingice/ajax-chosen/master/lib/ajax-chosen.min.js

echo "\nDone, now you must update the assets version!"