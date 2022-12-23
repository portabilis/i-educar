<?php

use App\Menu;
use Illuminate\Database\Migrations\Migration;

return new class () extends Migration {
    public function up()
    {
        Menu::where('process', '68')->update(['link' => '/web/enderecamento']);
        Menu::where('process', '753')->update(['link' => '/web/enderecamento/pais']);
        Menu::where('process', '754')->update(['link' => '/web/enderecamento/estado']);
        Menu::where('process', '755')->update(['link' => '/web/enderecamento/municipio']);
        Menu::where('process', '759')->update(['link' => '/web/enderecamento/distrito']);
    }

    public function down()
    {
        Menu::where('process', '68')->update(['link' => '/intranet/educar_enderecamento_index.php']);
        Menu::where('process', '753')->update(['link' => '/intranet/public_pais_lst.php']);
        Menu::where('process', '754')->update(['link' => '/intranet/public_uf_lst.php']);
        Menu::where('process', '755')->update(['link' => '/intranet/public_municipio_lst.php']);
        Menu::where('process', '759')->update(['link' => '/intranet/public_distrito_lst.php']);
    }
};
