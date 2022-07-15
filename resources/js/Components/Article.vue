<template>

    <div
        class="article"
        
        :class="{
            'article--border-red': disliked,
            'article--border-green': liked,
            'hide': removed,
        }"
    >

        <img v-if="image" class="article__image" :src="image" :alt="title">

        <Link v-if="href" :href="href" class="article__title">{{id}} - {{title}}</Link>
        <p class="article__title" v-else>{{id}} - {{title}}</p>


        <div class="article__content">
            <div v-html="content"></div>
        </div>

        <div class="article__meta article__meta--border-top">

            <div class="article__meta-item">
                <input type="number" class="button" min="1" max="10" v-model="sendRating">
                <button class="button button--fill" @click.prevent="updateRating">Опценить</button>
            </div>
            
            <div class="article__meta-item">Рейтинг: {{rating}}</div>


            <div class="article__meta-item article__meta-item--ml-auto" v-if="removable">
                <button class="button button--color-red" @click.prevent="remove">Удалить</button>
            </div>
        </div>
    </div>
</template>

<script>
import { Link } from '@inertiajs/inertia-vue3';
export default {
    props:{
        id:{
            type: Number,
        },

        href:{
            type: String,
        },
        
        image:{
            type: String,
        },
        
        title:{
            type: String,
        },
        
        content:{
            type: String,
        },
        
        rating:{
            type: Number,
        },
        
        removable:{
            type: Boolean,
            default: false
        },
    },
    emits: ['ratingUpdated', 'removed'],
    components:{
        Link
    },
    data() {
        return {
            liked: false,
            disliked: false,
            removed: false,
            sendRating: 10,
        }
    },
    watch:{

    },
    methods:{
        showError(message = 'Ошибка'){
            alert(message);
        },
        async updateRating(){
            let url = route('api.articles.rating.update', this.id);
            try {
                let article = (await (axios.put(url, {
                    'article': this.id,
                    'rating': this.sendRating
                }))).data;
                
                if(this.rating < article.attributes.rating){
                    this.showLike();
                }

                if(this.rating > article.attributes.rating){
                    this.showDislike();
                }

                this.$emit('articleUpdated', article);
                
            } catch (error) {
                let errorMessage = 'Ошибка. Рейтинг не обновлен';
                this.showError(errorMessage);
                return [];
            }
        },
        async like(){
            this.updateRating(1)
        },
        async dislike(){
            this.updateRating(-1)
        },
        async remove(){
            let url = route('api.articles.delete', this.id);
            try {
                await (axios.delete(url, {
                    'article': this.id,
                }));

                this.$emit('articleDeleted', this.id);
                
            } catch (error) {
                let errorMessage = 'Ошибка';
                this.showError(errorMessage);
                return [];
            }
        },
        
        
        showLike(callback = false){
            this.liked = true;
            setTimeout(p => {
                this.liked = false;

                if(callback){
                    callback();
                }
            }, 2000)
        },
        showRemove(callback = false){
            this.removed = true;
            setTimeout(p => {
                this.removed = false;
                if(callback){
                    callback();
                }
            }, 2000)
        },
        showDislike(callback = false){
            this.disliked = true;
            setTimeout(p => {
                this.disliked = false;
                if(callback){
                    callback();
                }
            }, 2000)
        },
    }
}
</script>

<style scoped>
    .article{
        --border-color: #333;
        padding: 16px;
        border: 1px var(--border-color) solid;

        color: #333;
        text-decoration: none;
        font-size: 16px;

        transition: border-color .25s;
    }

    
    .article--border-red{
        --border-color: red;
    }
    .article--border-green{
        --border-color: lightgreen;
    }

    .article__title{
        font-weight: bold;
        margin: 0 0 10px;
        font-size: 1.2em;
        text-decoration: none;
        color: inherit;
        display: block;
    }

    .article__image{
        display: block;
        margin: 0 auto;
        max-width: 100%;
        height: auto;
        margin-bottom: 16px;
        object-fit: contain;
        object-position: center center;
        max-height: 50vh;
    }

    .article__meta{
        display: flex;
        align-items: center;
        flex-wrap: wrap;
        gap: 10px;
    }

    .article__meta--border-top{
        padding-top: 16px;
        margin-top: 16px;
        border-top: #333 1px solid;
    }

    .article__meta-item--ml-auto{
        margin-left: auto;
    }
</style>