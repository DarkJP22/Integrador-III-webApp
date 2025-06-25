<template>
  <div>
    <a
      v-for="(link, index) in links"
      :key="link.url+index"
      :href="'#'"
      @click.prevent="actionPage(link)"
      v-html="link.label"
      class=" tw-appearance-none tw-outline-none tw-bg-white tw-border-y tw-border-gray-300 tw-text-gray-500 hover:tw-bg-gray-50 tw-relative tw-inline-flex tw-items-center tw-px-4 tw-py-2 tw-font-medium focus:tw-outline-none focus:tw-border-teal-500/30 focus:tw-ring focus:tw-ring-teal-500/20 focus:tw-z-10"
      :class="{ '!tw-text-gray-300 tw-cursor-not-allowed focus:tw-ring-0': ! link.url, 'tw-z-10 !tw-bg-teal-500/10 hover:tw-bg-teal-500/10 tw-border-teal-500-light tw-text-teal-500 tw-border' : link.active }"
      
    ></a>
  
  </div>
</template>

<script>
export default {
    props: ['links'],
    data() {
        return {
            page: 1,
           
        };
    },
    watch: {
        links() {
            //this.page = this.links.current_page;
            // this.prevUrl = this.dataSet.prev_page_url;
            // this.nextUrl = this.dataSet.next_page_url;
        },

        page() {
        
            return this.$emit('changed', this.page);
        }
    },
    methods:{
        actionPage(link){
            if(!link.url) return;
            if(link.label.includes('Siguiente')){
                this.page++;
                return;
            }
            if(link.label.includes('Anterior')){
                this.page--;
                return;
            }
            
            this.page = parseInt(link.label);
          
        }
    }
};

</script>