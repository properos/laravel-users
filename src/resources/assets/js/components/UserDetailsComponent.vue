<template>
    <div>
        <div class="card" style="min-height: 70vh;">
            <div class="card-header">
                <div class="float-left">
                    <h4 class="card-title">User Details</h4>
                </div>
                <div class="float-right">
                    <ul class="list-inline mb-0" style="margin-top:-5px;">
                        <li><a href="#" @click="showPage(pagination.current_page)" style="padding: 0 8px;"><i class="ft-rotate-cw"></i></a></li>
                    </ul>
                </div>
            </div>
            <div class="card-content">
                <div class="card-body">
                    <form class="form" v-on:submit.prevent="update()">
                        <h5 class="form-section" ><i class="ft-info"></i>Info</h5>
                        <div class="row">
                            <div class="col-image">
                                <fieldset class="form-group">
                                    <div class="avatar-wrapper">
                                        <img class="profile-pic-admin" src="" />
                                        <div class="upload-button-admin">
                                            <i class="fa fa-arrow-circle-up" aria-hidden="true"></i>
                                        </div>
                                        <input class="file-upload-admin" type="file" accept="image/*" ref="picture" />
                                    </div>
                                </fieldset>
                            </div>
                            <div class="col-data">
                                <div class="row">
                                    <div class="col-12 col-sm-6">
                                        <div class="form-group">
                                            <label for="firstname">Firstname</label>
                                            <input v-model="form.firstname" type="text" class="form-control" id="firstname" name="firstname">
                                            <span v-if="errors.firstname.invalid" class="message-error">{{errors.firstname.message}}</span>
                                        </div>
                                    </div>
                                    <div class="col-12 col-sm-6">
                                        <label for="lastname">Lastname</label>
                                        <input v-model="form.lastname" type="text" class="form-control" id="lastname" name="lastname">
                                        <span v-if="errors.lastname.invalid" class="message-error">{{errors.lastname.message}}</span>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-12 col-sm-6">
                                        <div class="form-group">
                                            <label for="email">Email</label>
                                            <input v-model="form.email" type="text" class="form-control" id="email" name="email">
                                            <span v-if="errors.email.invalid" class="message-error">{{errors.email.message}}</span>
                                        </div>
                                    </div>
                                    <div class="col-12 col-sm-6">
                                        <div class="form-group">
                                            <label for="phone">Phone</label>
                                            <input v-model="form.phone" type="text" class="form-control" id="phone" name="phone">
                                        </div>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-12 col-sm-8">
                                        <div class="form-group">
                                            <label for="status">Affiliate</label>
                                            <select id="select2-users-on-details" class="select2-placeholder form-control" style="width: 100%"></select>
                                            <span v-if="errors.affiliate_id.invalid" class="error_message">{{errors.affiliate_id.message}}</span>
                                        </div>
                                    </div>
                                    <div class="col-6 col-sm-4">
                                        <div class="form-group">
                                            <label for="city">Percent</label>
                                            <input v-model="form.percent" type="text" class="form-control" id="percent">
                                        </div>
                                    </div>
                                    <div class="col-6 col-sm-4">
                                        <div class="form-group">
                                            <label for="status">Status</label>
                                            <select v-model="form.status" class="form-control" id="status" name="status">
                                                <option value="active">Active</option>
                                                <option value="inactive">Inactive</option>
                                            </select>
                                        </div>
                                    </div>
                                    <div class="col-6 col-sm-4">
                                        <div class="form-group">
                                            <button class="btn btn-outline-blue" style="margin-top:26px;" type="button" data-toggle="modal" data-target="#changePasswordModal"><i class="fa fa-key"></i> New Password</button>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <h5 class="form-section" style="margin-top: 20px;"><i class="fa fa-gear"></i>Roles</h5>
                        <div class="row">
                            <div class="col-6 col-sm-4 col-md-3">
                                <div class="form-group">
                                    <label for="role">Role</label>
                                    <select v-model="role_id" @change="changeAction" type="text" class="form-control" placeholder="Choose a role">
                                        <option value="">Choose a role</option>
                                        <option v-for="(_role, index) in roles" :value="_role.id" :key="index">{{_role.label}}</option>
                                    </select>
                                    <span v-if="errors.role_id.invalid" class="message-error">{{errors.role_id.message}}</span>
                                </div>
                            </div>
                            <div class="col-6 col-sm-4 col-md-3">
                                <label for="restrictable_type">Type</label>
                                <select v-model="restrictable_type" id="select-restrictable-type" @change="onChangeTypeSelect" type="text" class="form-control" placeholder="Choose a type">
                                    <option value="">Choose a type</option>
                                    <option v-for="(restrictable, index) in restrictables" :value="index" :key="index">{{restrictable.label}}</option>
                                </select>
                                <span v-if="errors.restrictable_type.invalid" class="message-error">{{errors.restrictable_type.message}}</span>
                            </div>
                            <div class="col-12 col-sm-4 col-md-4" v-show="restrictable_type">
                                <label for="restrictable_id" v-if="restrictable_type">{{restrictables[restrictable_type].label}}</label>
                                <select id="select2-restrictable-id" class="select2-placeholder form-control input-bordered" style="width:100%;">
                                    <option value="">All</option>                                    
                                </select>
                                <span v-if="errors.restrictable_id.invalid" class="message-error">{{errors.restrictable_id.message}}</span>
                            </div>
                            <div class="col-2">
                                <div class="form-group">
                                    <button class="btn btn-outline-blue" type="button" @click="addRole()" style="margin-top:26px;"><i class="fa fa-plus"></i> Role</button>
                                </div>
                            </div>
                        </div>
                        <div v-if="Object.keys(current_roles).length > 0">
                            <div class="row">
                                <ul class="list-group" style="width:100%">
                                    <li class="list-group-item" style="width:100%">
                                        <ul class="list-group">
                                            <div class="row" v-for="(_role, index) in current_roles" :key="index">
                                                <div class="col-12" v-if="_role.restrictables.length > 0">
                                                    <li  class="list-group-item" style="width:100%" v-for="(_rest, index) in _role.restrictables" :key="index">
                                                        <div class="row" >
                                                            <div class="col-2">
                                                                <span :class="restrictables[_rest.restrictable_type].icon" style="font-size: 40px; color: gray; margin-left: 10%"></span>
                                                            </div>
                                                            <div class="col-8" v-if="_rest.restrictable">
                                                                <div class="row">
                                                                    <div class="col-4" style="margin-top: 5px">
                                                                        <p>
                                                                            <b>{{ _role.label | capitalize}}</b>
                                                                        </p>
                                                                    </div>
                                                                    <div class="col-8" style="margin-top: 5px">
                                                                        <p>
                                                                            <b>{{ restrictables[_rest.restrictable_type].getText(_rest.restrictable)}}</b>
                                                                        </p>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                            <div class="col-8" style="margin-top: 5px" v-else>
                                                                <p>
                                                                    <b>{{ _role.label | capitalize}}</b>
                                                                </p>
                                                            </div>
                                                            <div class="col-2 text-right" style="margin-top:5px;">
                                                                <a @click="delRole({role_id:_role.id,restrictable_type:_rest.restrictable_type,restrictable_id:_rest.restrictable_id})" href="#">
                                                                    <i class="ft-trash" style="font-size:23px;color:#c62828;"></i>
                                                                </a>
                                                            </div>
                                                        </div>
                                                    </li>
                                                </div>
                                                <div class="col-12" v-else>
                                                     <li  class="list-group-item" style="width:100%">
                                                        <div class="row" >
                                                            <div class="col-2">
                                                                <span class="fa fa-gear" style="font-size: 40px; color: gray; margin-left: 10%"></span>
                                                            </div>
                                                            <div class="col-8" style="margin-top: 5px">
                                                                <p>
                                                                    <b>{{ _role.label | capitalize}}</b>
                                                                </p>
                                                            </div>
                                                            <div class="col-2 text-right" style="margin-top:5px;">
                                                                <a @click="delRole({role_id:_role.id,restrictable_type:null,restrictable_id:null})" href="#">
                                                                    <i class="ft-trash" style="margin-top:5px;font-size:23px;color:#c62828;"></i>
                                                                </a>
                                                            </div>
                                                        </div>
                                                     </li>
                                                </div>
                                            </div>
                                        </ul>
                                    </li>
                                </ul>
                            </div>
                        </div>
                        <div class="form-actions float-left" style="border-top: none;" v-if="false">
                            <button type="button" class="btn btn-outline-danger"><i class="fa fa-trash-o"></i> <span class="d-none d-sm-inline"> Delete</span></button>
                        </div>
                        <div class="form-actions float-right" style="border-top: none;">
                            <button type="button" class="btn btn-outline-blue-grey"  @click="backToList()"><i class="fa fa-chevron-left"></i> <span class="d-none d-sm-inline">Back</span></button> 
                            <button type="submit" class="btn btn-primary"><i class="fa fa-floppy-o"></i> <span class="d-none d-sm-inline">Save</span></button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
        <password-reset-modal :user_id="user_id" type="user"></password-reset-modal>
    </div>
</template>

<script>
    import UserServices from '../services/UserServices.js';
    import UserResetPasswordModalComponent from './UserResetPasswordModalComponent';

    export default {
        props: ['roles', 'user_id'],
        mixins: [UserServices],
        components:{
            "password-reset-modal":UserResetPasswordModalComponent
        },
        data() {
            return {
                form: {
                    firstname: '',
                    lastname: '',
                    email: '',
                    phone: '',
                    status: 'active',
                    affiliate_id: 0,
                    percent: '0',
                    avatar: null,
                },
                role_id:'',
                current_roles: [],
                affiliate:{},
                restrictables: {
                    unit: {
                        icon:"ft-home",
                        label: "Unit",
                        fields: ['id', 'address', 'apt'],
                        getText(item){
                            return item.address + " Unit " + item.apt;
                        }
                    }
                },
                restrictable_type: "",
                restrictable_id: "",
                errors: {
                    firstname: {
                        invalid: false,
                        message: ""
                    },
                    lastname: {
                        invalid: false,
                        message: ""
                    },
                    email: {
                        invalid: false,
                        message: ""
                    },
                    affiliate_id: {
                        invalid: false,
                        message: ""
                    },
                    percent: {
                        invalid: false,
                        message: ""
                    },
                    role_id: {
                        invalid: false,
                        message: ""
                    },
                    restrictable_type: {
                        invalid: false,
                        message: ""
                    },
                    restrictable_id: {
                        invalid: false,
                        message: ""
                    }
                }
            }
        },

        methods: {
            update(){
                var hasError = false;
                var required = {
                    firstname:['string'],
                    lastname:['string'],
                    email:['string','email'],
                    phone:['string']
                }

                for (const key in this.form) {
                    if (this.errors.hasOwnProperty(key)) {
                        this.errors[key].invalid = false;
                        this.errors[key].message = '';
                    }
                    if (required.hasOwnProperty(key)) {
                        for (const filter in required[key]) {
                            if(filter == 'string'){
                                this.form[key] = (this.form[key] + '').trim();
                                if(this.form[key] != ''){
                                    this.errors[key].invalid = true;
                                    this.errors[key].message = 'This field is required';
                                    hasError = true;
                                    break;
                                }
                            }
                            if(filter == 'email'){
                                this.form[key] = (this.form[key] + '').trim();
                                if(!Helpers.validateEmail(this.form[key])){
                                    this.errors[key].invalid = true;
                                    this.errors[key].message = "The email isn't valid";
                                    hasError = true;
                                    break;
                                }
                            }
                        }
                    }
                }
                if(!hasError){
                    Helpers.block();
                    this.updateUser(this.user_id, Helpers.jsonToFormData(this.form), this.updateUserCallback);
                }
            },
            updateUserCallback(response){
                if(response.status > 0){
                    toastr.success(response.message, 'Success');
                }else{
                    if (Helpers.isAssoc(response.errors)) {
                        let errors = [];
                        for (var i in response.errors) {
                            if(this.errors.hasOwnProperty(i)){
                                this.errors[i].invalid = true;
                                this.errors[i].message = response.errors[i][0];
                            }
                            errors.push('<span>' + response.errors[i] + '</span></br>')
                        }
                        toastr.error(errors, 'Some error(s) has occurred');
                    } else {
                        toastr.error(response.errors[0], 'An error has occurred');
                    }
                }
                Helpers.unblock();
            },
            changeAction() {
                if (this.role_id > 0) {
                    $("#select-restrictable-type").prop("disabled", false);
                } else {
                    $("#select-restrictable-type").prop("disabled", true);
                }
            },
            onChangeTypeSelect() {
                if (this.restrictable_type == '') {
                    $("#sselect2-restrictable-id").prop("disabled", true);
                    this.destroySelect2Restrictable();
                } else {
                    $("#sselect2-restrictable-id").prop("disabled", false);
                    this.initSelect2Restrictable();
                }
            },
            addRole() {
                var params = {
                    role_id:0,
                    restrictable_type:this.restrictable_type,
                    restrictable_id:this.restrictable_id
                };
                
                if(this.role_id > 0){
                    params['role_id'] = this.role_id;
                    this.assignRole(this.user_id, params, this.addRoleCallback)
                }else{
                    this.errors.role_id.invalid = true;
                    this.errors.role_id.message = "Role is required.";
                }
            },
            addRoleCallback(response) {
                if(response.status > 0){
                    this.current_roles = [];
                    for (const key in response.data) {
                        this.current_roles.push(response.data[key]);
                    }

                    this.role_id = '';
                    this.restrictable_type = '';
                    this.restrictable_id = '';

                    $('#select2-restrictable-id').val(null).trigger('change');

                    toastr.success(response.message, 'Success');
                }else{
                    swal({
                        title:'Error!', 
                        text: response.errors[0], 
                        icon: 'error'
                    }).then((result) => {
                        if (result) {
                            this.backToList()
                        }
                    });
                }
                Helpers.unblock();
            },
            delRole(params) {
                swal({
                    title: "Are you sure?",
                    text: "Please confirm you want to remove this role.",
                    icon: "warning",
                    buttons: {
                        cancel: {
                            text: "Cancel",
                            value: null,
                            visible: true,
                            className: "btn-warning",
                            closeModal: true,
                        },
                        confirm: {
                            text: "Yes",
                            value: true,
                            visible: true,
                            className: "btn-primary",
                            closeModal: true
                        }
                    }
                }).then(isConfirm => {
                    if (isConfirm) {
                        Helpers.block();
                        this.removeRole(this.user_id, params, this.delRoleCallback);
                    }
                });
            },
            delRoleCallback(response) {
                if (response.status > 0) {
                    this.current_roles = [];
                    for (const key in response.data) {
                        this.current_roles.push(response.data[key]);
                    }
                    toastr.success(response.message, 'Success');
                } else {
                    if (Helpers.isAssoc(response.data.errors)) {
                        let errors = [];
                        for (var i in response.data.errors) {
                            var string
                            errors.push('<span>' + response.data.errors[i] + '</span></br>')
                        }
                        toastr.error(errors, 'Some error(s) has occurred');
                    } else {
                        toastr.error(response.data.errors[0], 'An error has occurred');
                    }
                }
                Helpers.unblock();
            },
            getUserCallback(response){
                if(response.status > 0){
                    for(let i in this.form){
                        if(i == 'avatar' && response.data[i]){
                            $('.profile-pic-admin').attr('src', Helpers.getEnv('MIX_CDN_URL', '') + '/' + response.data[i]);
                        }else{
                            this.form[i] = response.data[i];
                        }
                    }
                    this.affiliate = Object.assign({}, this.affiliate, response.data.affiliate);
                    this.current_roles = [];
                    for (const key in response.data.current_roles) {
                        this.current_roles.push(response.data.current_roles[key]);
                    }
                    this.initSelect2();
                }else{
                    swal({
                        title:'Error!', 
                        text: response.errors[0], 
                        icon: 'error'
                    }).then((result) => {
                        if (result) {
                            this.$router.push({ name: 'users_list'});
                        }
                    });
                }
                Helpers.unblock();
            },
            destroySelect2(){
                $("#select2-users-on-details").select2('destroy');
            },
            initSelect2(){
                var self = this;
                $("#select2-users-on-details").select2({
                    placeholder: 'Select affiliate',
                    ajax: {
                        url: self.getPath() + '/users/search',
                        dataType: 'json',
                        delay: 250,
                        headers: {
                            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                        },
                        type: 'POST',
                        data: function (params) {
                            params.term = (typeof params.term == 'undefined') ? '' : params.term;
                            var terms = {
                                query: '+*' + params.term.trim().replace(" ", "* +*") + '*', // search term
                                fields: ['id', 'firstname','lastname'],
                                page: params.page,
                                limit: 5
                            } 
                            
                            terms['where'] = [
                                    ['id','<>',self.user_id]
                            ];
                            
                            return terms;
                        },
                        processResults: function (response, params) {
                            params.page = params.page || 1;
                            return {
                                results: response.data,
                                pagination: {
                                    more: (params.page) < response.pagination.total_pages
                                }
                            };
                        },
                        cache: true
                    },
                    escapeMarkup: function (markup) { return markup; }, 
                    templateResult: function (repo) {
                        if (repo.loading) return repo.text;
                        var markup = "<div class='select2-result-repository clearfix'>" + repo.firstname +' '+ repo.lastname + "</div>";
                        return markup;
                    }, 
                    templateSelection: function (repo) {
                        if (repo.firstname) {
                            self.form.affiliate_id = repo.id;
                            return repo.firstname + ' ' + repo.lastname;
                        } else {
                            return repo.text;
                        }
                    }
                });

                if(this.form.affiliate_id > 0){
                    let data = {
                        text: this.affiliate.firstname + " " + this.affiliate.lastname,
                        name: this.affiliate.firstname + " " + this.affiliate.lastname,
                        id: this.affiliate.id
                    };
                    var option = new Option(data.text, data.id, true, true);
                    $("#select2-users-on-details").append(option).trigger('change');
                    $("#select2-users-on-details").trigger({
                    type: 'select2:select',
                        params: {
                        data: data
                        }
                    });
                }
            },
            destroySelect2Restrictable(){+
                $("#select2-restrictable-id").select2('destroy');
            },
            initSelect2Restrictable(){
                var self = this;
                $("#select2-restrictable-id").select2({
                    placeholder: 'Select ones',
                    ajax: {
                        url: self.getPath() + '/users/' + self.restrictable_type + '/search',
                        dataType: 'json',
                        delay: 250,
                        headers: {
                            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                        },
                        type: 'POST',
                        data: function (params) {
                            params.term = (typeof params.term == 'undefined') ? '' : params.term;
                            var terms = {
                                query: '+*' + params.term.trim().replace(" ", "* +*") + '*', // search term
                                page: params.page,
                                fields: self.restrictables[self.restrictable_type].fields,
                                limit: 5
                            }
                            
                            return terms;
                        },
                        processResults: function (response, params) {
                            params.page = params.page || 1;
                            return {
                                results: response.data,
                                pagination: {
                                    more: (params.page) < response.pagination.total_pages
                                }
                            };
                        },
                        cache: true
                    },
                    escapeMarkup: function (markup) { return markup; }, 
                    templateResult: function (repo) {
                        if (repo.loading) return repo.text;
                        return "<div class='select2-result-repository clearfix'>" + self.restrictables[self.restrictable_type].getText(repo)  + "</div>";
                    }, 
                    templateSelection: function (repo) {
                        self.restrictable_id = repo.id;
                        if(repo.id > 0){
                            return self.restrictables[self.restrictable_type].getText(repo);
                        }else{
                            return repo.text;
                        }
                    }
                });
            },
            backToList(){
               return this.$router.push({ name: 'users_list'});
            },
            readURL(input) {
                if (input.files && input.files[0]) {
                    this.form.avatar = input.files[0];
                    var reader = new FileReader();

                    reader.onload = function (e) {
                        $('.profile-pic-admin').attr('src', e.target.result);
                    }
                    reader.readAsDataURL(input.files[0]);
                }
            }
        },
        filters: {
            capitalize: function (value) {
                if (!value) return ''
                value = value.toString()
                return value.charAt(0).toUpperCase() + value.slice(1)
            }
        },
        created() {
            Helpers.block();
            this.getUser(this.user_id,{
                fields:['id', 'firstname', 'lastname', 'phone', 'email', 'affiliate_id', 'avatar', 'percent', 'status'],
                with:['affiliate'],
                appends:{
                    getCurrentRoles:{
                        as: "current_roles"
                    }
                }
            },this.getUserCallback);
        },
        mounted() {
            var self = this;
            this.$nextTick(function () {
                $(".file-upload-admin").on('change', function () {
                    self.readURL(this);
                });
                $(".upload-button-admin").on('click', function () {
                    $(".file-upload-admin").click();
                });
                $(".nav-item").removeClass("active");
                $('#users').addClass('active')
            });
        }
    }
</script>

<style>
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

    label {
        font-size: 12px
    }

    .select2-selection__rendered {
        font-size: 12px !important;
    }

    .vcenter {
        display: inline-block;
        vertical-align: middle;
        float: none;
    }
</style>
<style scoped>
    input,
    select,
    textarea {
        font-size: 12px;
    }

    .logo-list {
        width: 80px;
        height: auto;
    }

    .avatar-wrapper {
        margin: 0px auto;
        position: relative;
        height: 150px;
        width: 150px;
        border-radius: 50%;
        overflow: hidden;
        box-shadow: 1px 1px 15px -5px black;
        transition: all .3s ease;
    }

    .avatar-wrapper:hover {
        transform: scale(1.05);
        cursor: pointer;
    }

    .avatar-wrapper:hover .profile-pic-admin {
        opacity: .5;
    }

    .avatar-wrapper .profile-pic-admin {
        height: 100%;
        width: 100%;
        transition: all .3s ease;
    }

    .avatar-wrapper .profile-pic-admin:after {
        font-family: FontAwesome;
        content: "\f007";
        top: 0;
        left: 0;
        width: 100%;
        height: 100%;
        position: absolute;
        font-size: 150px;
        background: #ecf0f1;
        color: #34495e;
        text-align: center;
    }

    .avatar-wrapper .upload-button-admin {
        position: absolute;
        top: 0;
        left: 0;
        height: 100%;
        width: 100%;
    }

    .avatar-wrapper .upload-button-admin .fa-arrow-circle-up {
        position: absolute;
        font-size: 179px;
        top: -15px;
        left: 2;
        text-align: center;
        opacity: 0;
        -webkit-transition: all .3s ease;
        transition: all .3s ease;
        color: #34495e;
    }

    .avatar-wrapper .upload-button-admin:hover .fa-arrow-circle-up {
        opacity: .9;
    }

    .col-image{
        padding: 0px 15px;
        width: 160px;
    }
    .col-data{
        padding: 0px 15px;
        width: calc(100% - 160px);
    }

    @media (max-width: 576px) {
        .col-image{
            padding: 0px 15px;
            margin: 0px auto;
            width: 100%;
        }
        .col-data{
            padding: 0px 15px;
            width: 100%;
        }   
    }

</style>