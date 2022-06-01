import UserContainerComponent from './components/UserContainerComponent.vue';
import UserListComponent from './components/UserListComponent.vue';
import UserCreateComponent from './components/UserCreateComponent.vue';
import UserDetailsComponent from './components/UserDetailsComponent.vue';
import ProfileComponent from './components/ProfileComponent.vue';

export default [
    {
        path: process.env.MIX_ADMIN_WEB_PREFIX + '/users',
        component: UserContainerComponent,
        children: [
            {
                path: '/',
                component: UserListComponent,
                name: 'users_list'
            },
            {
                path: 'create',
                component: UserCreateComponent,
                name: 'create_user'
            },
            {
                path: ':user_id',
                component: UserDetailsComponent,
                name: 'user_details',
                props: true
            }
        ]
    },
    {
        path: process.env.MIX_ADMIN_WEB_PREFIX + '/my-profile',
        component: ProfileComponent,
        name: 'profile'
    }
    
]