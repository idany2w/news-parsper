<template>
    <Head title="Articles" />

    <div class="container">
        <div class="articles">

            <h1 class="articles__title">Articles</h1>

            <div class="articles__controls">
                <div class="articles__control">
                    <p>По сколько подгружать</p>
                    <input type="number" class="input" min="5" max="10" v-model="perPage">
                    <button class="button button--fill" @click.prevent="updatePerpage">Изменить</button>
                </div>
                <div class="articles__control">
                    <p>Интервал обновления</p>
                    <input type="number" class="input" min="5"  v-model="interval">
                    <button class="button button--fill" @click.prevent="updateInterval">Изменить</button>
                </div>
            </div>

            <div v-if="fetchArticlesError && articles.length == 0">
                Мы пока не можем отобразить для вас статьи
            </div>
            <div v-else class="articles__list" id="observer-list">
                <Article 
                    v-for="(item, index) in articles"
                    :key="index"

                    class="articles__list-item" 
                    :href="route('articles.show', item.id)"
                    :id="+item.id"
                    :title="item.attributes.title"
                    :content="item.attributes.content"
                    :rating="item.attributes.rating"
                    :removable="true"

                    @articleUpdated="updateArticle"
                    @articleDeleted="deleteArticle"
                />
            </div>
            <div id="loader"></div>
        </div>

    </div>

</template>

<script >
import { Head, Link } from '@inertiajs/inertia-vue3';
import Article  from '../Components/Article.vue';

export default {
    components:{
        Article,
        Head,
        Link,
    },
    props: {
        title: String,
        likes: Number
    },
    data() {
        return {
            articles: [],
            page: 0,
            // TODO: сохранять perPage и interval в localstorage
            perPage: 5,
            interval: 60,
            intervalId: null,
            observer: null,
            fetchArticlesError: false,
        }
    },
    methods: {
        async fetchArticles(page = 1) {
            this.fetchArticlesError = false;

            try {
                let result = await (axios.get(route('api.articles.index'), {
                    params:{
                        'page': page,
                        'perPage': this.perPage
                    }
                }));
                return result.data.data;   
            } catch (error) {
                this.fetchArticlesError = true;
                return [];
            }
        },

        updateArticle(article) {
            let id = this.articles.findIndex(item => item.id === article.id);

            this.articles[id].attributes.rating = article.attributes.rating;
        },
        deleteArticle(id){
            let index = this.articles.findIndex(item => item.id == id);
            if(index != -1) this.articles.slice(index, 1);
        },
        async updatePerpage(){
            this.page = 1;

            if(this.perPage < 5){
                this.perPage = 5;
            }

            this.articles = await this.fetchArticles(this.page);
        },
        updateInterval(){
            if(this.intervalId !== null){
                clearInterval(this.intervalId);
            }

            if(this.interval < 5){
                this.interval = 5;
            }

            this.intervalId = setInterval(async ()=>{
                // TODO: сделать метод "Показать (n) yновых записей"
                this.articles = await this.fetchArticles();
            }, this.interval * 1000)
        },
        initObserver(){
            const loader = document.querySelector("#loader");
            
            this.observer = new IntersectionObserver(async ()=>{
                this.articles.push(...(await this.fetchArticles(++this.page)));
            }, {
                rootMargin: "50px",
            });

            this.$nextTick(() => {
                this.observer.observe(loader);
            });
        }
    },
    async created(){
        this.updateInterval();
    },
    mounted() {
        this.initObserver();
    },
    destroyed() {
        this.observer.disconnect();
    }
}
</script>

<style scoped>

.articles__title{
    margin: 25px 0;
    font-weight: bold;
    text-align: center;
    font-size: 100px;
}

.articles__list{
    display: flex;
    flex-direction: column;
    gap: 20px;
    
    padding: 20px 0;
}

.articles__controls{
    display: flex;
    flex-wrap: wrap;
    align-items: center;
    gap: 10px;
}
</style>
