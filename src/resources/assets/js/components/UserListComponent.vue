<template>
    <div class="card">
        <div class="card-header">
            <div class="float-left">
                <h4 class="card-title">User List</h4>
            </div>
            <div class="float-right">
                <ul class="list-inline mb-0" style="margin-top:-5px;">
                    <li><a href="#" @click="showPage(pagination.current_page)" style="padding: 0 8px;"><i class="ft-rotate-cw"></i></a></li>
                </ul>
            </div>
        </div>
        <div class="card-content">
            <div class="card-body">
                <div class="form-group mb-2 col-8 col-xs-12" style="padding:0;">
                    <button class="btn btn-outline-primary" type="button" @click="$router.push({ name: 'create_user'})"><i class="ft-users"></i> Add</button>
                </div>
                <form v-on:submit.prevent="showPage(1)">
                    <div class="row">
                        <div class="col-12 col-md-9 " style="padding-left:21px; padding-right:21px;">
                            <label>Search</label>
                            <input type="text" v-model="query" class="form-control" id="query" placeholder="Name, Phone & Email">
                        </div>
                        <div class="col-12 col-md-3 " style="padding-left:21px; padding-right:21px;">
                            <div class="form-group" style="width: 100%;">
                                <label>Roles</label>
                                <select class="form-control" data-placeholder="Select roles..." v-model="role" style="width: 100%" @change="showPage(1)"> 
                                    <option value="all">All</option>
                                    <option v-for="(_role, index) in roles" :key="index" :value="_role.name" style="text-transform:capitalize;">{{_role.label}}</option>
                                </select>
                            </div>
                        </div>
                    </div>
                </form>
                <div class="table-responsive">
                    <table id="recent-customers" class="table table-hover mb-0">
                        <thead>
                            <tr>
                                <th>Full Name</th>
                                <th>Phone</th>
                                <th>Email</th>
                                <th class="text-center">Status</th>
                                <th class="text-center">Audit</th>
                                <th class="text-center"></th>
                            </tr>
                        </thead>
                        <tbody v-if="list.length">
                            <tr v-for="(item, index) in list" :key="index">
                                <td class="text-truncate">{{item.firstname}} {{item.lastname}}</td>
                                <td class="text-truncate">{{item.phone}}</td>
                                <td class="text-truncate">{{item.email}}</td>
                                <td class="text-center">
                                    <span :class="{'badge badge-primary' : item.status == 'active',
                                    'badge badge-danger' : item.status == 'inactive'}">{{item.status}}
                                    </span>
                                </td>
                                <td style="text-align:center;"><a :href="'/api/admin/su/'+item.id"><i class="fa fa-eye text-navy"></i></a></td>
                                <td style="text-align:center;"><a class="cursor-pointer" @click="$router.push({ name: 'user_details', params: { user_id: item.id }})"><i class="fa fa-pencil"></i></a></td>
                            </tr>
                        </tbody>
                        <tbody v-else>
                            <tr>
                                <th colspan="6" class="text-center">No data</th>
                            </tr>
                        </tbody>
                    </table>
                </div>                
            </div>
            <div class="col-12 text-center">
                <div v-html="this.print_show()"></div>
            </div>
            <div class="col-12">
                <pagination
                    :pagination="pagination"
                    p_classes="justify-content-center"
                    @onFirst="first" 
                    @onLast="last"
                    @onShowPage="showPage"
                >
                </pagination>
            </div>
        </div>
    </div>
</template>

<script>
    import UserServices from '../services/UserServices.js';
    import PaginationMixin from "../../../../../misc/pagination";
    import PaginationViewComponent from "../../../../../components/PaginationViewComponent";

    export default {
        mixins:[UserServices, PaginationMixin],
        components: {
            'pagination': PaginationViewComponent
        },
        props:['roles'],
        data() {
            return {
                list: [],
                Helpers: Helpers,
                query: "",
                role:'all'
            }
        },
        methods: {
            getList(params) {
                Helpers.block();
                params["fields"] = ['id', 'firstname', 'lastname', 'phone', 'email', 'users', 'status'];

                this.query = this.query.trim();

                if (this.query != "") {
                    let query = this.query;
                    params["query"] = {
                        value: "+*" + query.replace(/\s+/g, "* +*") + "*", // search term
                        fields: ['firstname', 'lastname', 'phone', 'email']
                    };
                }

                if(this.role != '' && this.role != 'all'){
                    params['with'] = {
                        roles:{
                            where:[
                                ['name', this.role]
                            ]
                        }
                    }
                }else{
                    params['with'] = {};
                }

                this.caching(params);
                this.getUserList(
                    params,
                    this.getUserListCallback
                );
            },
            getUserListCallback(response) {
                if (response.status > 0) {
                    this.list = response.data;
                    this.calcPages(response.pagination);
                } else {
                    toastr.error(response.errors[0], 'An error has occurred');
                }
                Helpers.unblock();
            },
            getUsersCallback(code, response, errors) {
                if (code == '200') {
                    if(response.data.exported){
                        swal("Success!", "The report is being exported and will be sent to your email.", "success");
                    }else{
                        if(response.data.length > 0){
                            this.paginator = response.pagination;
                        }else{
                            this.paginator= {
                                total: 0,
                                per_page: 2,
                                from: 1,
                                to: 0,
                                current_page: 1
                            };
                        }
                        this.users = response.data;
                    }
                }else{
                    toastr.error(response.errors[0], 'An error has occurred');
                }
                Helpers.unblock();
            }
        },
        created() {
            this.paginationInit(15, this.getList);
            this.query = this.getQueryCached();
        },
        mounted() {
            this.showPage(1, false);
            this.$nextTick(function () {
                $(".nav-item").removeClass("active");
                $('#users').addClass('active')
            })
        }
    }
</script>

<style>
    .icon-store {
        width: 30px;
        height: auto;
    }

    .icon-amazon {
        width: 50px;
        height: auto;
    }

    .input-error-select {
        color: #d61212 !important;
        border: 1px solid #b60707 !important;
        border-radius: 5px
    }

    .message-error {
        color: #d61212;
        float: right;
        padding-top: 10px;
        font-size: 12px;
    }
    .icons-custom{
        width:20px;
        height:20px;
    }
</style>