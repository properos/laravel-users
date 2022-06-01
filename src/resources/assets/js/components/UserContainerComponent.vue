<template>
    <div>
        <div class="row mt-1">
            <div class="col-12">
                <transition :name="transitionName">
                    <router-view :roles="roles" :morph_map="morph_map"></router-view>
                </transition>
            </div>
        </div>
    </div>
</template>

<script>
    export default {
        props:['roles', 'morph_map'],
        data() {
            return {
                transitionName: 'slide-left',
            };
        },
        methods: {
            beforeRouteUpdate(to, from, next) {
                const toDepth = to.path.split('/').length
                const fromDepth = from.path.split('/').length
                this.transitionName = toDepth < fromDepth ? 'slide-right' : 'slide-left'
                next()
            }
        },
        mounted() {

        }
    };
</script>