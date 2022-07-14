<script >
import { Head, Link } from '@inertiajs/inertia-vue3';

export default {
  props: {
    title: String,
    likes: Number
  },
  data() {
    return {
      articles: [],
    }
  },
  methods: {
    async fetchArticles() {
        let result = await (axios.get(`/api/articles/`));
        return result.data.data;
    }
  },
  async created(){
    this.articles = await this.fetchArticles();
  }
}
</script>

<template>
    <Head title="Articles" />

    <!-- <Link :href="route('article')" >
        Log in
    </Link> -->

    <div class="container">
        <div class="articles">

            <h1 class="articles__title">Articles</h1>

            <div class="articles__list">
                <a class="articles__list-item article" v-for="(item, index) in articles" :key="index" href="{{item.links.self}}">
                    <p class="article__title">{{item.id}} - {{item.attributes.title}}</p>
                    <p class="article__introtext">{{item.attributes.content}}</p>

                    <div class="article__meta">
                        <div class="article__meta-item">Рейтинг: {{item.attributes.rating}}</div>
                    </div>
                </a>
            </div>
        </div>

    </div>

</template>


<style scoped>
.container{
    width: 100%;
    max-width: 1000px;
    margin: 0 auto;
}

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

.article{
    display: flex;
    flex-direction: column;
    padding: 16px;
    border: 1px #333 solid;
<<<<<<< HEAD

    color: #333;
    text-decoration: none;
=======
>>>>>>> bb7b42566ba44d85be5e79ec01da0a6427290091
}

.article__title{
    font-weight: bold;
    margin-block: 10px;
}
</style>
