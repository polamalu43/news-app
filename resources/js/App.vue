<template>
    <v-app>
        <HeaderGlobal :host="host" :auth-status="authStatus"/>
            <v-main>
                <v-container>
                    <router-view
                        :datas="datas"
                        :host="host"
                        :auth-status="authStatus"
                        :liked-by-user='likedByUser'
                        @clicked-article-id="clickedArticleId"
                    />
                </v-container>
            </v-main>
        <FooterGlobal />
    </v-app>
</template>

<script>
import HeaderGlobal from './global/HeaderGlobal';
import FooterGlobal from './global/FooterGlobal';

export default {
    name: "App",
    components: {
        HeaderGlobal,
        FooterGlobal
    },
    data: ()=> ({
        datas: {},
        host: location.protocol + '//' + location.host + '/',
    }),
    created() {
        this.selectArticles();
    },
    mounted(){
    },
    methods: {
        async selectArticles(){
            try {
                await axios.get("/api/selectArticles")
                    .then(
                        response => {
                            this.datas = response.data;
                        }
                    );
            } catch (error) {
                console.log(error);
            }
        },
        clickedArticleId(e){
            this.changeIsRecommended(e);
        },
        async changeIsRecommended(e){
            try {
                await axios.post("/api/changeIsRecommended", {
                    articleId: e,
                }).then(response => console.log(response));
            } catch (error) {
                console.log(error);
            }
        }
    },
    mounted : function(){
    },
    props: {
        authStatus: Boolean,
        likedByUser: Boolean
    },
    computed: {
    },
    watch: {
        datas: function(n, o){
        },
    }
};
</script>
