<?php

use App\Menu;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up()
    {
        Schema::table('', function (Blueprint $table) {
            Menu::where('process', '753')->update(['link' => '/web/enderecamento/pais']);
            Menu::where('process', '754')->update(['link' => '/web/enderecamento/estado']);
            Menu::where('process', '755')->update(['link' => '/web/enderecamento/municipio']);
            Menu::where('process', '759')->update(['link' => '/web/enderecamento/distrito']);
        });
    }

    public function down()
    {
        Schema::table('', function (Blueprint $table) {
            Menu::where('process', '753')->update(['link' => '/intranet/public_pais_lst.php']);
            Menu::where('process', '754')->update(['link' => '/intranet/public_uf_lst.php']);
            Menu::where('process', '755')->update(['link' => '/intranet/public_municipio_lst.php']);
            Menu::where('process', '759')->update(['link' => '/intranet/public_distrito_lst.php']);
        });
    }
};
