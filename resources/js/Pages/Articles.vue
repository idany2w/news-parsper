<template>
    <Head title="Articles" />

    <div class="container">
        <div class="articles">

            <h1 class="articles__title">Articles</h1>

            <div class="">
                <input type="number" class="button" min="1" max="10" v-model="newPerPage">
                <button class="button button--fill" @click.prevent="updatePerpage">обновить</button>
            </div>

            <div v-if="fetchArticlesError && articles.length == 0">
                Мы пока не можем отобразить для вас статьи
            </div>
            <div v-else class="articles__list" >
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
            perPage: 3,
            newPerPage: null,
            fetchArticlesError: false
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

            let index = this.articles.findIndex(item => item.id === id);
            
            if(index != -1)

            this.articles = this.articles.slice(index,1);
            
            


        },
        scroll () {
            const observer = new IntersectionObserver(async ()=>{
                this.articles.push(...(await this.fetchArticles(++this.page)));
            }, {
                rootMargin: "50px",
            });

            const loader = document.querySelector("#loader");
            observer.observe(loader);
        },
        async updatePerpage(){
            this.page = 1;
            this.perPage = +this.newPerPage;
            this.newPerPage = null;
            this.articles = await this.fetchArticles();
        }
    },
    async created(){
        this.articles = await this.fetchArticles();

        setInterval(async ()=>{
            this.articles = await this.fetchArticles();
        }, 30000)
    },
    mounted() {
        this.scroll();
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
</style>
