import router from "./router.js";

const Gallery = () => import('@/pages/Gallery.vue');
const ViewPicture = () => import('@/pages/ViewPicture.vue');

const routes = [
    {
        name: 'home',
        path: '/',
        component: Gallery,
    },
    {
        name: 'view',
        path: '/picture/:id',
        props(route) {
            const props = {...route.params};
            props.id = parseInt(props.id);
            return props;
        },
        component: ViewPicture,
    }
];
export default routes;
