var path = process.env.MIX_ADMIN_WEB_API_PREFIX;
export default {
    methods: {
        getPath(){
            return path;
        },
        getUserList(params, callBackHandler) {
            axios({
                method: 'post',
                url: path + '/users/search',
                data:params,
            }).then(function (response) {
                callBackHandler(response.data);
            }.bind(this)).catch(function (error) {
                callBackHandler(Helpers.unknownError(error));
            })
        },
        getProfile(params, callBackHandler) {
            axios({
                method: 'get',
                url: path + '/my-profile/edit',
                params: params,
            }).then(function (response) {
                callBackHandler(response.data);
            }.bind(this)).catch(function (error) {
                callBackHandler(Helpers.unknownError(error));
            })
        },
        getUser(id, params, callBackHandler) {
            axios({
                method: 'get',
                url: path + '/users/' + id,
                params: params,
            }).then(function (response) {
                callBackHandler(response.data);
            }.bind(this)).catch(function (error) {
                callBackHandler(Helpers.unknownError(error));
            })
        },
        updateUser(id, params, callBackHandler) {
            axios({
                method: 'post',
                url: path + '/users/' + id,
                data: params,
                headers: {'Content-Type': 'multipart/form-data' }
            }).then(function (response) {
                callBackHandler(response.data);
            }.bind(this)).catch(function (error) {
                callBackHandler(Helpers.unknownError(error));
            })
        },
        updateProfile(params, callBackHandler) {
            axios({
                method: 'post',
                url: path + '/my-profile/update',
                data: params,
                headers: {'Content-Type': 'multipart/form-data' }
            }).then(function (response) {
                callBackHandler(response.data);
            }.bind(this)).catch(function (error) {
                callBackHandler(Helpers.unknownError(error));
            })
        },
        deleteUser(id, callBackHandler) {
            axios({
                method: 'delete',
                url: path + '/users/' + id + '/delete'                
            }).then(function (response) {
                callBackHandler(response.data);
            }.bind(this)).catch(function (error) {
                callBackHandler(Helpers.unknownError(error));
            })
        },
        createUser(params, callBackHandler){
            axios({
                method: 'post',
                url: path + '/users',
                data: params,
                headers: {'Content-Type': 'multipart/form-data' }
            }).then(function (response) {
                callBackHandler(response.data);
            }.bind(this)).catch(function (error) {
                callBackHandler(Helpers.unknownError(error));
            });
        },
        changeUserPassword(id, params, callBackHandler) {
            axios({
                method: 'post',
                url: path + '/users/' + id + '/change-password',
                data: params
            }).then(response => {
                return callBackHandler(response.data);
            }).catch((error) => {
                return callBackHandler(error);
            });
        },
        changeProfilePassword(params, callBackHandler) {
            axios({
                method: 'post',
                url: path + '/my-profile/change-password',
                data: params
            }).then(response => {
                return callBackHandler(response.data);
            }).catch((error) => {
                return callBackHandler(error);
            });
        },
        assignRole(id, params, callBackHandler) {
            axios({
                method: 'post',
                url: path + '/users/' + id + '/role',
                data: params
            }).then(response => {
                return callBackHandler(response.data);
            }).catch((error) => {
                return callBackHandler(error);
            });
        },
        removeRole(id, params, callBackHandler) {
            axios({
                method: 'post',
                url: path + '/users/' + id + '/role/delete',
                data: params
            }).then(response => {
                return callBackHandler(response.data);
            }).catch((error) => {
                return callBackHandler(error);
            });
        },
        //ApiCalls
        // getLedgersCall(data, callBackHandler) {
        //     axios({
        //         method: 'post',
        //         url: '/api/admin/users/ledgers/search',
        //         data: data
        //     }).then(response => {
        //         return callBackHandler(response.data);
        //     }).catch((error) => {
        //         return callBackHandler('002', error);
        //     });
        // },
    }
}