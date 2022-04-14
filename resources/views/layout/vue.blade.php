<div id="quick-search" class="vue-template">
    <div class="quick-search">
        <vue-multiselect placeholder="Busca rápida"
                         select-label=""
                         selected-label=""
                         deselect-label=""
                         label="label"
                         @search-change="asyncFind"
                         @select="dispatchAction"
                         :options="options">
            <span slot="noResult">Sem resultados.</span>
            <template slot="option" slot-scope="props">
                <a :href="props.option.link">@{{ props.option.label }}</a>
            </template>
        </vue-multiselect>
    </div>
</div>
<div id="quick-searchM" class="vue-template">
    <div class="quick-search">
        <vue-multiselect placeholder="Busca rápida"
                         select-label=""
                         selected-label=""
                         deselect-label=""
                         label="label"
                         @search-change="asyncFind"
                         @select="dispatchAction"
                         :options="options">
            <span slot="noResult">Sem resultados.</span>
            <template slot="option" slot-scope="props">
                <a :href="props.option.link">@{{ props.option.label }}</a>
            </template>
        </vue-multiselect>
    </div>
</div>

<script src="{{ Asset::get('js/axios.min.js') }}"></script>
<script src="{{ Asset::get('js/vue.min.js') }}"></script>
<script src="{{ Asset::get('js/vue-multiselect.min.js') }}"></script>
<script>
    Vue.component('vue-multiselect', window.VueMultiselect.default);
    Vue.component('quick-search', {
        methods: {
            asyncFind (query) {
                axios.get('/module/Api/menu/', {
                    params:  {
                        query : query,
                        oper : 'get',
                        resource : 'menu-search'
                    }
                }).then((res) => {
                    this.options = res.data.menus;
                });
            },
            dispatchAction (element) {
                if (element.link) {
                    window.location.href = element.link;
                }
            }
        },
        data: function () {
            return {
                options: [
                    {
                        link: '',
                        label: 'Sem resultados.'
                    }
                ],
                value: null
            }
        },
        template: '#quick-search'
    });

    new Vue({
        el: '#ieducar-quick-search'
    });

</script>
<script>
    Vue.component('vue-multiselect', window.VueMultiselect.default);
    Vue.component('quick-search', {
        methods: {
            asyncFind (query) {
                axios.get('/module/Api/menu/', {
                    params:  {
                        query : query,
                        oper : 'get',
                        resource : 'menu-search'
                    }
                }).then((res) => {
                    this.options = res.data.menus;
                });
            },
            dispatchAction (element) {
                if (element.link) {
                    window.location.href = element.link;
                }
            }
        },
        data: function () {
            return {
                options: [
                    {
                        link: '',
                        label: 'Sem resultados.'
                    }
                ],
                value: null
            }
        },
        template: '#quick-searchM'
    });

    new Vue({
        el: '#ieducar-quick-searchM'
    });
</script>

