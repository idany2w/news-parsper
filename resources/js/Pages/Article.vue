<template>
    <Head title="Article" />
    <Link :href="this.route('articles.index')" class="button back-button">Назад к списку новостей</Link>
    <Article
        :id="currentArticle.id"
        :image="currentArticle.attributes.image"
        :title="currentArticle.attributes.title"
        :content="currentArticle.attributes.content"
        :rating="currentArticle.attributes.rating"

        v-on:articleUpdated="updateArticle"
    />
    <Link :href="this.route('articles.index')" class="button back-button">Назад к списку новостей</Link>
</template>
<script >

import { Head, Link } from '@inertiajs/inertia-vue3';
import Article  from '../Components/Article.vue';

export default {
    props: {
        article: Object,
    },
    data() {
        return {
            currentArticle: this.article,
        }
    },
    components:{
        Article,
        Head,
        Link,
    },
    methods:{
        updateArticle(article){
            this.currentArticle = article;
        }
    }
}
</script>

<style scoped>
    .back-button{
        margin: 10px 0;
    }
</style>