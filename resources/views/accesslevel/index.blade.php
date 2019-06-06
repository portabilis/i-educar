@extends('layout.default')

@section('content')

    <div id="app-content">
        <access-level-menu></access-level-menu>
    </div>

@endsection

@push('styles')
    <link rel="stylesheet" type="text/css" href="{{ Asset::get('css/ieducar.css') }}" />
@endpush

@push('end')
    <div id="access-level-menu-radiogroup" class="vue-template">
        <div id="radiogroup">
            <div class="radiogroup">
                <input v-if="process.allow >= 3" :id="'acl-3' + hash" :checked="value === 3" @input="$emit('input', value === 3 ? 2 : 3)" type="checkbox" :name="'processes[' + process.process + ']'" value="3">
                <label v-if="process.allow >= 3" :for="'acl-3' + hash">
                    <span class="radiogroup-icon"><i class="fa" :class="{ 'fa-check': value >= 3, 'fa-remove': value < 3 }"></i></span> <span>Exclui</span>
                </label>
                <input v-if="process.allow >= 2" :id="'acl-2' + hash" :checked="value === 2" @input="$emit('input', value === 2 ? 1 : 2)" type="checkbox" :name="'processes[' + process.process + ']'" value="2">
                <label v-if="process.allow >= 2" :for="'acl-2' + hash">
                    <span class="radiogroup-icon"><i class="fa" :class="{ 'fa-check': value >= 2, 'fa-remove': value < 2 }"></i></span> <span>Cadastra</span>
                </label>
                <input v-if="process.allow >= 1" :id="'acl-1' + hash" :checked="value === 1" @input="$emit('input', value === 1 ? 0 : 1)" type="checkbox" :name="'processes[' + process.process + ']'" value="1">
                <label v-if="process.allow >= 1" :for="'acl-1' + hash">
                    <span class="radiogroup-icon"><i class="fa" :class="{ 'fa-check': value >= 1, 'fa-remove': value < 1 }"></i></span> <span>Visualiza</span>
                </label>
            </div>
        </div>
    </div>
    <div id="access-level-menu" class="vue-template">
        <div class="access-level-menu">

                <form
                    id="formcadastro"
                    @if($userType->getKey())
                    action="{{ route('usertype.update', ['userType' => $userType]) }}"
                    @else
                    action="{{ route('usertype.create') }}"
                    @endif
                    method="post">

                @if($userType->getKey())
                    @method('put')
                @endif

                <table width="100%" cellpadding="0" cellspacing="0">
                    <tbody>
                    <tr>
                        <td class="formdktd" colspan="2" height="24"><b>Novo </b></td>
                    </tr>
                    <tr id="tr_nm_tipo">
                        <td class="formmdtd">
                            <span class="form">Tipo de Usuário</span><span class="campo_obrigatorio">*</span>
                        </td>
                        <td class="formmdtd">
                            <span class="form"><input class="obrigatorio" type="text" name="name" v-model="userType.nm_tipo" size="40" maxlength="255"> </span>
                        </td>
                    </tr>
                    <tr id="tr_nivel">
                        <td class="formlttd">
                            <span class="form">Nível</span><span class="campo_obrigatorio">*</span>
                        </td>
                        <td class="formlttd">
                            <span class="form">
                                <select v-model="userType.nivel" class="obrigatorio" name="level">
                                    @foreach(Auth::user()->type->getLevelDescriptions() as $value => $label)
                                    <option value="{{ $value }}">{{ $label }}</option>
                                    @endforeach
                                </select>
                            </span>
                        </td>
                    </tr>
                    <tr id="tr_descricao">
                        <td class="formmdtd">
                            <span class="form">Descrição</span>
                        </td>
                        <td class="formmdtd">
                            <span class="form">
                                <textarea v-model="userType.descricao" class="geral" name="description" cols="37" rows="5"></textarea>
                            </span>
                        </td>
                    </tr>
                    </tbody>
                </table>
                <table width="100%" cellpadding="0" cellspacing="0">
                    <tbody>
                        <tr><td colspan="2"><hr></td></tr>
                        <tr>
                            <td class="formlttd">
                                <span class="form">Procure pelo nome do menu: </span><input type="text" v-model="search" placeholder="Digite o menu que procura" style="width: 300px;">
                            </td>
                            <td class="formlttd">
                                <div style="clear: both;"><small class="text-muted">Marcar opção para todos</small></div>
                                <div class="radiogroup">
                                    <button type="button" @click="menus.forEach(menu => menu.processes.forEach(item => processes[item.process] = 3))">Exclui</button>
                                    <button type="button" @click="menus.forEach(menu => menu.processes.forEach(item => processes[item.process] = 2))">Cadastra</button>
                                    <button type="button" @click="menus.forEach(menu => menu.processes.forEach(item => processes[item.process] = 1))">Visualiza</button>
                                    <button type="button" @click="menus.forEach(menu => menu.processes.forEach(item => processes[item.process] = 0))">Sem permissão</button>
                                </div>
                            </td>
                        </tr>
                        <template v-for="menu in menus">
                            <tr><td colspan="2"><hr></td></tr>
                            <tr>
                                <td class="formlttd" height="24">
                                    <b style="font-size: 16px">@{{ menu.menu.title }}</b>
                                </td>
                                <td class="formlttd" height="24">
                                    <div style="clear: both;"><small class="text-muted">Marcar opção para todos os itens: <b>@{{ menu.menu.title }}</b></small></div>
                                    <div class="radiogroup">
                                        <button type="button" @click="menu.processes.forEach(item => processes[item.process] = 3)">Exclui</button>
                                        <button type="button" @click="menu.processes.forEach(item => processes[item.process] = 2)">Cadastra</button>
                                        <button type="button" @click="menu.processes.forEach(item => processes[item.process] = 1)">Visualiza</button>
                                        <button type="button" @click="menu.processes.forEach(item => processes[item.process] = 0)">Sem permissão</button>
                                    </div>
                                </td>
                            </tr>
                            <tr v-for="(process, i) in menu.processes" v-if="process.title.toLowerCase().includes(search.toLowerCase())">
                                <td :class="{ formmdtd: i % 2 == 0, formlttd: i % 2 != 0 }">
                                    <span class="form">@{{ process.title }}</span>
                                    <br>
                                    <a :href="process.link"><sup>@{{ process.description }}</sup></a>
                                </td>
                                <td :class="{ formmdtd: i % 2 == 0, formlttd: i % 2 != 0 }" width="500">
                                    <access-level-menu-radiogroup :process="process" v-model="processes[process.process]"></access-level-menu-radiogroup>
                                </td>
                            </tr>
                        </template>
                        <tr><td class="tableDetalheLinhaSeparador" colspan="2"></td></tr>
                        <tr class="linhaBotoes">
                            <td colspan="2" align="center">
                                <button class="btn-green" type="submit">Salvar</button>
                                <a class="btn" href="{{ route('usertype.index') }}">Cancelar</a>
                                @can('remove', 554)
                                @if($userType->getKey())
                                <button class="btn" type="button" onclick="this.form._method.value = 'delete'; this.form.action = '{{ route('usertype.delete', ['userType' => $userType]) }}'; this.form.submit()">Excluir</button>
                                @endif
                                @endcan
                            </td>
                        </tr>
                    </tbody>
                </table>
            </form>
        </div>
    </div>

    <script>
        Vue.component('access-level-menu-radiogroup', {
            template: '#access-level-menu-radiogroup',
            props: {
                value: {
                    type: Number,
                    default: 0
                },
                process: {
                    type: Object
                }
            },
            data: function () {
                return {
                    radio: 0,
                    hash: Math.random().toString(36).substr(2, 9)
                };
            }
        });

        Vue.component('access-level-menu', {
            template: '#access-level-menu',
            data: function () {
                return {
                    menus: @json($menus),
                    userType: @json($userType),
                    processes: @json($processes),
                    search: '',
                    radio: 0
                };
            }
        });

        new Vue({
            el: '#app-content'
        });
    </script>
@endpush
