<tr id="tr_file">
    <td class="formmdtd" valign="top"><span class="form">Arquivos</span></td>
    <td class="formmdtd" valign="top">
        <span class="form">
            @if(isset($files))
                @foreach($files as $file)
                    <div>
                        <span id="file_info{{$file->id}}">
                            {{$file->original_name}} adicionado em {{$file->created_at->format('d/m/Y')}}:
                            <a class="decorated" id="link_delete_file_{{$file->id}}" style="cursor: pointer; margin-left: 10px;">Excluir</a>
                            <a class="decorated" id="link_view_file_{{$file->id}}" target="_blank" rel="noopener" href="{{$file->url}}" style="cursor: pointer; margin-left: 10px;">Visualizar</a>
                        </span>
                    </div>
                @endforeach
            @endif
            <input @if($disabled) disabled @endif class="inputfile inputfile-buttom" name="file" id="file" type="file" size="40" value="">
            <label id="file" for="file"><span></span> <strong>Escolha um arquivo</strong></label>&nbsp;<br>
            <img src="imagens/indicator.gif" style="margin-top: 3px; display: none;">
            <span id="span-documento" style="font-style: italic; font-size: 10px;">
                São aceitos arquivos nos formatos jpg, png, jpeg e pdf. Tamanho máximo: 2MB
            </span>
            <input type="hidden" name="file_url" id="file_url"/>
            <input type="hidden" name="file_url_deleted" id="file_url_deleted"/>
        </span>
    </td>
</tr>

<script>
$j('#file').on('change', prepareUpload);
var $loadingFile = $j('<img>')
    .attr('src', 'imagens/indicator.gif')
    .css('margin-top', '3px')
    .hide()
    .insertBefore($j('#span-file'));

var $arrayFile = [];
var $arrayDeletedFiles = [];

function deleteFile(event) {
    var removeId = this.id.replace(/\D/g, '') - 1;
    var fileUrl = $j.parseJSON($j('#file_url').val());
    fileUrl.splice(removeId, 1);
    $j('#file_url').val(JSON.stringify(fileUrl));
    $j('#file').val('').removeClass('success');
    messageUtils.notice('Arquivo excluído com sucesso!');
    $j('#file' + event.data.i).hide();
}

function addFile(url, originalName, extension, size, data) {
    $index = $arrayFile.length;
    $id = $index + 1;

    var dataFile = '';

    if (data) {
        dataFile = ' adicionado em ' + data;
    }

    $arrayFile[$arrayFile.length] = $j('<div>')
        .append($j('<span>')
        .html(originalName + ' ' + dataFile + ':')
        .attr('id', 'file' + $id)
        .append($j('<a>')
        .html('Excluir')
        .addClass('decorated')
        .attr('id', 'link_remove_file_' + $id)
        .css('cursor', 'pointer')
        .css('margin-left', '10px')
        .click({i: $id}, deleteFile))
        .append($j('<a>')
        .html('Visualizar')
        .addClass('decorated')
        .attr('id', 'link_view_file_' + $id)
        .attr('target', '_blank')
        .attr('href', linkUrlPrivada(url))
        .css('cursor', 'pointer')
        .css('margin-left', '10px'))
    ).insertBefore($j('#file'));

    makeUrlFile(url, originalName, extension, size, data);
}

function makeUrlFile(url, originalName, extension, size, data) {
    var fileUrl = $j.parseJSON($j('#file_url').val());
    var arrayPush = [];
    if (fileUrl){
        $j.each(fileUrl, function (key, file) {
            arrayPush.push(file);
        });
    }

    var fileUrlNew = {
        url : url,
        originalName : originalName,
        extension : extension,
        size : size,
        data : data
    };

    arrayPush.push(fileUrlNew);
    $j('#file_url').val(JSON.stringify(arrayPush));
}

function prepareUpload(event) {
    $j('#file').removeClass('error');
    uploadFiles(event.target.files);
}

function uploadFiles(files) {
    if (files && files.length > 0) {
        $j('#file').attr('disabled', 'disabled');
        $j('#btn_enviar').attr('disabled', 'disabled').val('Aguarde...');
        $loadingFile.show();
        messageUtils.notice('Carregando arquivo...');

        var data = new FormData();
        $j.each(files, function (key, value) {
            data.append('file', value);
        });

        $j.ajax({
            url: '/upload?file=',
            type: 'POST',
            data: data,
            cache: false,
            dataType: 'json',
            processData: false,
            contentType: false,
            success: function (dataResponse) {
                if (dataResponse.error) {
                    $j('#file').val("").addClass('error');
                    messageUtils.error(dataResponse.error);
                } else {
                    messageUtils.success('Arquivo carregado com sucesso');
                    $j('#file').addClass('success');
                    addFile(
                        dataResponse.file_url,
                        dataResponse.file_original_name,
                        dataResponse.file_extension,
                        dataResponse.file_size,
                        currentDate()
                    );
                }

            },
            error: function (dataResponse) {
                errorMessage = $j.parseJSON(dataResponse.responseText).errors.file;
                $j('#file').val("").addClass('error');
                errorMessage.each(function(message) {
                    messageUtils.error(message);
                });
            },
            complete: function () {
                $j('#file').removeAttr('disabled');
                $loadingFile.hide();
                $j('#btn_enviar').removeAttr('disabled').val('Gravar');
            }
        });
    }
}

function currentDate() {
    var today = new Date();
    var dd = today.getDate();
    var mm = today.getMonth() + 1;
    var yyyy = today.getFullYear();

    if (dd < 10) {
        dd = '0' + dd
    }

    if (mm < 10) {
        mm = '0' + mm
    }

    return dd + '/' + mm + '/' + yyyy;
}

$j('[id^="link_delete_file"]').click(function(id, val){
    idElement = this.id.replace(/\D/g, '');
    $arrayDeletedFiles.push(idElement);
    $j('#file_info' + idElement).remove();
    $j('#file_url_deleted').val($arrayDeletedFiles);
});
</script>
