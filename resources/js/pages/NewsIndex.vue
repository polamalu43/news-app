<template>
    <div>
        <v-tabs v-cloak>
            <v-tab
                v-for="(country, index) in countryies"
                :key="index"
                @click="changeCountry(index)"
            >{{ country }}</v-tab>
        </v-tabs>

        <v-card class="pb-5 mt-5">
            <v-tabs
                background-color="primary"
                dark
                show-arrows
            >
                <v-tabs-slider color="teal lighten-3"></v-tabs-slider>
                <v-tab
                    v-for="(category, index) in categoryies"
                    :key="index"
                    @click="changeCategory(index)"
                >{{ category }}</v-tab>
            </v-tabs>
            <div class="d-flex mb-6 flex-wrap px-5 cols=10">
               <v-card
                    v-for="article in articles"
                    :key = "article.id"
                    class="mx-auto mt-5"
                    max-width="350"
                >
                    <v-img
                        v-if="article.thumbnail"
                        :src="article.thumbnail"
                        height="250px"
                    ></v-img>
                    <v-img
                        v-else
                        src="http://127.0.0.1:8000/images/no-image.png"
                        height="250px"
                    ></v-img>

                    <v-card-title>
                        <a :href="article.url">
                            {{ article.title }}
                        </a>
                    </v-card-title>

                    <v-card-subtitle class="mb-3">
                        <span v-if="article.author">発信元：{{ article.author }}</span>
                    </v-card-subtitle>

                    <div class="ml-1 mb-1" v-if="authStatus">
                        <v-img width="30px"
                        style="cursor: pointer;"
                        :src="url + iconRecommend(article.isRecommended)"
                        @click="recommendClick(article.id, $event)"
                        >
                        </v-img>
                    </div>

                    <v-expand-transition v-if="article.description">
                        <div>
                            <v-divider></v-divider>
                            <v-card-text>
                                {{ article.description }}
                            </v-card-text>
                        </div>
                    </v-expand-transition>
                </v-card>
            </div>
        </v-card>
    </div>
</template>

<script>
    export default {
        data: () => ({
            countryNo: 0,
            categoryNo: 0,
            datasFlag: false,
            clickElem: null,
            url: '',
            articleId: null,
            articleElem: null
        }),
        methods: {
            recommendClick(articleId, event){
                this.changeIcon(event);
                this.$emit('clicked-article-id', articleId)
            },
            changeIcon(event){
                const elem = event.target.parentNode.querySelector('.v-image__image');
                const imageUrl = window.getComputedStyle(elem).getPropertyValue('background-image')
                const imageFileName = imageUrl.slice(imageUrl.lastIndexOf('/') + 1).replace('")', '');
                elem.style.backgroundImage = 'url("' + this.url + 'icon_recommend_on.png")';
                if(imageFileName == 'icon_recommend_off.png'){
                    elem.style.backgroundImage = 'url("' + this.url + 'icon_recommend_on.png")';
                }else{
                    elem.style.backgroundImage = 'url("' + this.url + 'icon_recommend_off.png")';
                }
            },
            changeCountry(index){
                this.countryNo = index;
            },
            changeCategory(index){
                this.categoryNo = index;
            },
            iconRecommend(isRecommended){
                let iconPath = 'icon_recommend_off.png';
                if(isRecommended) iconPath = 'icon_recommend_on.png';

                return iconPath;
            }
        },
        props: {
            datas: Object,
            host: String,
            authStatus: Boolean,
            likedByUser: Boolean
        },
        created(){
            this.url = this.host + 'images/';
        },
        computed: {
            articles() {
                if(this.datas.articles){
                    return this.datas.articles[this.countryies[this.countryNo]][this.categoryies[this.categoryNo]];
                }
            },
            countryies() {
                return this.datas.countryies;
            },
            categoryies() {
                return this.datas.categoryies;
            },
        },
        watch: {
            datas: function(n, o){
                console.log(n, o)
            },
        }
    }
</script>

<style scoped>
    [v-cloak]{
        display:none;
    }
    .expand{
        transition: all 1s;
    }
    .cardActionHidden{
        height: 0;
        padding: 0;
    }
</style>
