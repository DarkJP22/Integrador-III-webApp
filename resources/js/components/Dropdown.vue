<template>
    <div class="g-dropdown">
        <div class="g-dropdown-trigger" @click.prevent="isOpen = ! isOpen" aria-haspopup="true" :aria-expanded="isOpen">
            <slot name="trigger"></slot>
        </div>

        <transition name="pop-out-quick">
            <ul v-show="isOpen" class="g-dropdown-menu absolute mt-2 py-2 rounded shadow z-10" :class="classes">
                <li v-for="item in items" :key="item.value" :class="{ 'disabled': item.disabled }" @click="selectItem(item)"> {{ item.label }}</li>
            </ul>
        </transition>
    </div>
</template>

<script>
export default {
    props: ['classes', 'items'],
    data() {
        return {
            isOpen: false
        };
    },
    watch: {
        isOpen(isOpen) {
            if (isOpen) {
                document.addEventListener(
                    'click',
                    this.closeIfClickedOutside
                );
            }
        }
    },
    methods: {
        closeIfClickedOutside(event) {
            if (!event.target.closest('.g-dropdown')) {
                this.isOpen = false;
                document.removeEventListener('click', this.closeIfClickedOutside);
            }
        },
        selectItem(item) {
            if (!item.disabled) {
                this.$emit('itemSelected', item);
                this.isOpen = false;
            }

        }
    }
};
</script>

<style scoped>
ul {
    margin: 0;
    padding: 0;
    list-style: none;
    background: white;
    max-height: 25vh;
    overflow-y: auto;
    width: 180px;
    line-height: 2.75rem;
    border: 1px solid #dcdcdc;
}

@media screen and (max-width: 768px) {

    /** fix temp **/
    ul {
        position: static !important;
    }

}

li {
    cursor: pointer;
    padding: 0 1rem;
}

li.disabled {
    cursor: not-allowed !important;
    cursor: -moz-not-allowed !important;
    cursor: -webkit-not-allowed !important;
    background-color: #dcdcdc !important;
}

.w-full {
    width: 100%;
}

.relative {
    position: relative;
}

.absolute {
    position: absolute;
}

.mt-2 {
    margin-top: .5rem;
}

.py-2 {
    padding-top: .5rem;
    padding-bottom: .5rem;
}

.rounded {
    border-radius: .5rem;
}

.shadow {
    box-shadow: 5px 10px 8pxrgba(0, 0, 0, .75);
}

.text-white {
    color: white;
}

.z-10 {
    z-index: 999;
}

.pop-out-quick-enter-active,
.pop-out-quick-leave-active {
    transition: all 0.4s;
}

.pop-out-quick-enter,
.pop-out-quick-leave-active {
    opacity: 0;
    transform: translateY(-7px);
}
</style>