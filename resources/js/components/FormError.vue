<template>
    <span class="help-block">
        <slot></slot>
    </span>
</template>

<script>
export default {
    props: ['errors'],
};
</script>
<style>
.help-block {
    color: #C23321;
}
</style>
