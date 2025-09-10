@php
    $isInstitucional = Auth::user() && (Auth::user()->isAdmin() || Auth::user()->isInstitutional());
@endphp

<tr class="formlttd">
    <td class="formlttd" valign="top" style="vertical-align: top !important;">
        <span class="form">Observações</span>
    </td>
    <td class="formlttd" valign="top" style="vertical-align: top !important;">
        @if($activeLookingId)
            <div class="new-message-container">
                <textarea
                    name="nova_observacao"
                    id="new_observation"
                    rows="3"
                    cols="60"
                    maxlength="900"
                    placeholder="Digite sua observação..."
                    aria-label="Nova observação"
                ></textarea>
                <div class="message-actions">
                    <input type="button" id="btn_enviar" value="Adicionar Observação" aria-label="Adicionar observação" style="margin: 0 !important; padding: 6px 16px !important;">
                </div>
            </div>
        @else
            <div class="new-message-container">
                <textarea
                    name="observacoes"
                    id="observacoes"
                    rows="3"
                    cols="60"
                    maxlength="900"
                    placeholder="Digite suas observações..."
                    aria-label="Observações"
                ></textarea>
            </div>
        @endif
    </td>
</tr>

@if($activeLookingId)
<tr class="formmdtd">
    <td class="formmdtd" valign="top" style="vertical-align: top !important;">
        <span class="form">Histórico de Observações</span>
    </td>
    <td class="formmdtd" valign="top" style="vertical-align: top !important;">
        <div class="messages" role="region" aria-label="Lista de observações">
            @forelse($messages as $message)
                @php
                    $canEdit = $isInstitucional || $message->user_id === Auth::id();
                @endphp

                <article class="message-item" id="message-{{ $message->id }}" role="article">
                    <div class="message-content-wrapper">
                        <div class="message-content" id="message-content-{{ $message->id }}">
                            {!! nl2br(htmlspecialchars($message->description)) !!}
                        </div>

                        <footer class="message-footer">
                            <div class="message-info">
                                @if($message->user)
                                    <span class="message-author">
                                        <i class="fa fa-user" aria-hidden="true"></i>
                                        {{ $message->user->name }}
                                    </span>
                                    <span class="message-date">
                                        | {{ $message->created_at->format('d/m/Y H:i') }}
                                    </span>
                                @else
                                    <span class="message-date">
                                        {{ $message->created_at->format('d/m/Y H:i') }}
                                    </span>
                                @endif
                            </div>

                            @if($canEdit)
                                <div class="message-actions">
                                    <button
                                        type="button"
                                        class="edit-message-modal"
                                        data-message-id="{{ $message->id }}"
                                        data-message-text="{{ htmlspecialchars($message->description) }}"
                                        title="Editar observação"
                                        aria-label="Editar observação"
                                    >
                                        Editar
                                    </button>
                                    <button
                                        type="button"
                                        class="delete-message-modal"
                                        data-message-id="{{ $message->id }}"
                                        title="Excluir observação"
                                        aria-label="Excluir observação"
                                    >
                                        Excluir
                                    </button>
                                </div>
                            @endif
                        </footer>
                    </div>
                </article>
            @empty
                <div class="no-messages">
                    <p>Nenhuma observação registrada.</p>
                </div>
            @endforelse
        </div>
    </td>
</tr>
@endif

<div id="edit-message-modal" style="display: none;">
    <p><label for="edit-message-textarea">Observação:</label></p>
    <textarea
        id="edit-message-textarea"
        rows="6"
        cols="50"
        maxlength="900"
        placeholder="Digite sua observação..."
        aria-label="Texto da observação"
    ></textarea>
</div>

<div id="delete-message-modal" style="display: none;">
    <p>Tem certeza que deseja excluir esta observação?</p>
</div>

<style>
#tr_messages td:first-child {
    vertical-align: top !important;
    padding-top: 8px !important;
}

#tr_messages td:first-child .form {
    display: block;
    margin-top: 0 !important;
    padding-top: 0 !important;
}

#tr_messages_history td:first-child {
    vertical-align: top !important;
    padding-top: 8px !important;
}

#tr_messages_history td:first-child .form {
    display: block;
    margin-top: 0 !important;
    padding-top: 0 !important;
}

.new-message-container {
    margin-bottom: 10px;
    max-width: 550px;
}

.new-message-container .message-actions {
    display: flex;
    justify-content: flex-end;
    margin-top: 4px;
}

.messages {
    max-width: 550px;
}

.no-messages {
    text-align: center;
    padding: 20px;
    color: #6c757d;
    font-style: italic;
}

.message-item {
    margin-bottom: 8px;
    background-color: white;
    border: 1px solid #e9ecef;
    border-radius: 6px;
    box-shadow: 0 1px 3px rgba(0,0,0,0.05);
    transition: all 0.2s ease;
    max-width: 100%;
}

.message-item:hover {
    box-shadow: 0 2px 6px rgba(0,0,0,0.1);
    border-color: #47728f;
}

.message-content-wrapper {
    padding: 8px 12px;
    max-width: 100%;
}

.message-content {
    line-height: 1.4;
    color: #2c3e50;
    font-size: 14px;
    margin-bottom: 6px;
    word-wrap: break-word;
    word-break: break-word;
    overflow-wrap: break-word;
    max-width: 100%;
}

.message-footer {
    display: flex;
    justify-content: space-between;
    align-items: center;
    padding-top: 6px;
    border-top: 1px solid #f1f3f4;
    font-size: 12px;
}

.message-info {
    display: flex;
    gap: 6px;
    color: #6c757d;
    align-items: center;
}

.message-author {
    font-weight: 600;
    color: #47728f;
}

.message-author i {
    margin-right: 3px;
    opacity: 0.7;
}

.message-date {
    color: #868e96;
    font-weight: 400;
    font-size: 12px;
}

.message-actions {
    display: flex;
    gap: 10px;
}

.message-actions button {
    background: none;
    border: none;
    color: #47728f;
    text-decoration: none;
    font-size: 12px;
    font-weight: 500;
    cursor: pointer;
    padding: 0;
}

.message-actions button:hover {
    color: #2c3e50;
    text-decoration: underline;
}

.message-actions .edit-message-modal:hover {
    color: #1976d2;
}

.message-actions .delete-message-modal:hover {
    color: #d32f2f;
}

#new_observation {
    border: 2px solid #e9ecef;
    border-radius: 6px;
    padding: 10px;
    font-size: 14px;
    transition: border-color 0.2s ease;
    resize: vertical;
    min-height: 70px;
    max-width: 550px;
    width: 100%;
    box-sizing: border-box;
}

#new_observation:focus {
    border-color: #47728f;
    outline: none;
    box-shadow: 0 0 0 3px rgba(71, 114, 143, 0.1);
}

#edit-message-textarea {
    width: 100%;
    border: 1px solid #cddce6;
    border-radius: 3px;
    padding: 8px;
    resize: vertical;
    box-sizing: border-box;
    font-family: inherit;
}

#edit-message-textarea:focus {
    border-color: #47728f;
    outline: none;
    box-shadow: 0 0 0 2px rgba(71, 114, 143, 0.1);
}
</style>

<script>
(function($) {
    $(document).ready(function() {
        var $novaObservacao = $j('#new_observation');
        var $addMessageBtn = $j('#btn_enviar');
        var $messages = $j('.messages');
        var $editTextarea = $j('#edit-message-textarea');

        var currentMessageId = null;

        function debounce(func, delay) {
            clearTimeout(window.debounceTimer);
            window.debounceTimer = setTimeout(func, delay);
        }

        function createMessageHtml(message) {
            var authorName = message.user ? message.user.name : '';
            var authorIcon = message.user ? '<i class="fa fa-user" aria-hidden="true"></i> ' : '';
            var createdAt = new Date(message.created_at).toLocaleString('pt-BR', {
                day: '2-digit',
                month: '2-digit',
                year: 'numeric',
                hour: '2-digit',
                minute: '2-digit'
            });

            var messageInfoHtml = message.user
                ? '<span class="message-author">' + authorIcon + authorName + '</span><span class="message-date">| ' + createdAt + '</span>'
                : '<span class="message-date">' + createdAt + '</span>';

            var actionsHtml = '<div class="message-actions">' +
                '<button type="button" class="edit-message-modal" data-message-id="' + message.id + '" data-message-text="' + message.description.replace(/"/g, '&quot;') + '" title="Editar observação" aria-label="Editar observação">Editar</button> ' +
                '<button type="button" class="delete-message-modal" data-message-id="' + message.id + '" title="Excluir observação" aria-label="Excluir observação">Excluir</button>' +
                '</div>';

            return '<article class="message-item" id="message-' + message.id + '" role="article">' +
                '<div class="message-content-wrapper">' +
                '<div class="message-content" id="message-content-' + message.id + '">' +
                message.description.replace(/\n/g, '<br>') +
                '</div>' +
                '<footer class="message-footer">' +
                '<div class="message-info">' + messageInfoHtml + '</div>' +
                actionsHtml +
                '</footer>' +
                '</div>' +
                '</article>';
        }

        function addMessage() {
            var observacao = $novaObservacao.val().trim();
            if (!observacao) {
                messageUtils.error('Por favor, digite uma observação.');
                return;
            }

            var activeLookingId = {{ $activeLookingId ?? 'null' }};
            if (!activeLookingId) {
                messageUtils.error('Erro: ID da busca ativa não encontrado.');
                return;
            }

            var $btn = $addMessageBtn;
            var originalText = $btn.val();
            $btn.prop('disabled', true).val('Salvando...');

            var options = {
                url: '/api/active-looking-messages',
                type: 'POST',
                dataType: 'json',
                data: {
                    messageable_type: 'App\\Models\\LegacyActiveLooking',
                    messageable_id: activeLookingId,
                    description: observacao
                },
                success: function(response) {
                    $novaObservacao.val('');

                    if (response.data && response.data.id) {
                        var newMessageHtml = createMessageHtml(response.data);
                        $messages.prepend(newMessageHtml);
                        $j('#message-' + response.data.id).hide().fadeIn(500);
                    } else {
                        $messages.load(window.location.href + ' .messages > *');
                    }

                    var successMessage = response.message || 'Observação adicionada com sucesso!';
                    messageUtils.success(successMessage);
                    $btn.prop('disabled', false).val(originalText);
                },
                error: function(xhr, status, error) {
                    var errorMessage = 'Erro ao adicionar observação.';
                    if (xhr.responseJSON && xhr.responseJSON.message) {
                        errorMessage = xhr.responseJSON.message;
                    } else if (xhr.status === 419) {
                        errorMessage = 'Erro de autenticação. Por favor, recarregue a página e tente novamente.';
                    } else if (xhr.status === 422) {
                        errorMessage = 'Dados inválidos. Verifique se a observação não está vazia.';
                    }
                    messageUtils.error(errorMessage);
                    $btn.prop('disabled', false).val(originalText);
                }
            };

            postResource(options);
        }

        $addMessageBtn.on('click', function(e) {
            e.preventDefault();
            addMessage();
        });

        $j(document).on('click', '.edit-message-modal', function(e) {
            e.preventDefault();
            currentMessageId = $j(this).data('message-id');
            var messageText = $j(this).data('message-text');

            $editTextarea.val(messageText);

            $j("#edit-message-modal").dialog({
                autoOpen: true,
                closeOnEscape: true,
                draggable: false,
                width: 560,
                modal: true,
                resizable: false,
                title: 'Editar Observação',
                buttons: {
                    "Salvar": function () {
                        var observacao = $editTextarea.val().trim();

                        if (!observacao) {
                            messageUtils.error('Por favor, digite uma observação.');
                            return;
                        }

                        if (!currentMessageId) {
                            messageUtils.error('Erro: ID da mensagem não encontrado.');
                            return;
                        }

                        var options = {
                            url: '/api/active-looking-messages/' + currentMessageId,
                            type: 'PUT',
                            dataType: 'json',
                            data: { description: observacao },
                            success: function(response) {
                                $j("#edit-message-modal").dialog("close");
                                $messages.load(window.location.href + ' .messages > *');
                                var successMessage = response.message || 'Observação editada com sucesso!';
                                messageUtils.success(successMessage);
                            },
                            error: function() {
                                messageUtils.error('Erro ao editar observação. Tente novamente.');
                            }
                        };

                        putResource(options);
                    },
                    "Cancelar": function () {
                        $j(this).dialog("close");
                    }
                },
                close: function () {
                    $j(this).dialog("destroy");
                }
            });
        });

        $j(document).on('click', '.delete-message-modal', function(e) {
            e.preventDefault();
            currentMessageId = $j(this).data('message-id');

            $j("#delete-message-modal").dialog({
                autoOpen: true,
                closeOnEscape: false,
                draggable: false,
                width: 560,
                modal: true,
                resizable: false,
                title: 'Confirmar Exclusão',
                buttons: {
                    "Excluir": function () {
                        if (!currentMessageId) {
                            messageUtils.error('Erro: ID da mensagem não encontrado.');
                            return;
                        }

                        var options = {
                            url: '/api/active-looking-messages/' + currentMessageId,
                            type: 'DELETE',
                            dataType: 'json',
                            success: function(response) {
                                $j("#delete-message-modal").dialog("close");
                                $j('#message-' + currentMessageId).fadeOut();
                                var successMessage = response.message || 'Observação excluída com sucesso!';
                                messageUtils.success(successMessage);
                            },
                            error: function() {
                                messageUtils.error('Erro ao excluir observação. Tente novamente.');
                            }
                        };

                        deleteResource(options);
                    },
                    "Cancelar": function () {
                        $j(this).dialog("close");
                    }
                },
                close: function () {
                    $j(this).dialog("destroy");
                }
            });
        });

        $novaObservacao.on('keydown', function(e) {
            if (e.ctrlKey && e.keyCode === 13) {
                e.preventDefault();
                addMessage();
            }
        });
    });
})(jQuery);
</script>
