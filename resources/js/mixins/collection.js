export default {
    data() {
        return {
            items: []
        };
    },

    methods: {
        add(item) {
            this.items.unshift(item);

            this.$emit('added');
        },

        update(item, index) {
          
            const indexItem = index ? index : _.findIndex(this.items, { 'id': item.id });
            
            this.items.splice(indexItem, 1, item);

            this.$emit('updated');
        },

        remove(index) {
            this.items.splice(index, 1);

            this.$emit('removed');
        }
    }
};