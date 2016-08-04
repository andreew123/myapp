var myApp = angular.module('myApp', []);
myApp.controller('appController', [
'$scope',
'$rootScope',
'$http',
'appFactory',
function(
    $scope,
    $rootScope,
    $http,
    appFactory
) {
    var vm = this;
    vm.init = init;
    vm.getProducts = getProducts;
    vm.sendForm = sendForm;
    vm.searchForm = searchForm;
    vm.getCategories = getCategories;
    vm.getConditions = getConditions;
    vm.deleteRow = deleteRow;
    vm.updateRow = updateRow;
    vm.getPages = getPages;
    vm.prevPage = prevPage;
    vm.nextPage = nextPage;
    vm.newProduct = {};
    vm.editProduct = {};
    vm.searchProduct = {};
    vm.products = {};
    vm.categories = {};
    vm.page = 0;

    function getProducts() {
        var data = { page: vm.page * 10 };
        appFactory.getProducts(data)
        .then(function(products) {
            console.log('products', products);
            vm.products = products;
        });
    }

    function getPages() {
        appFactory.getPages()
        .then(function(pages) {
            vm.pages = pages;
            console.log('pages', vm.pages);
        })
    }

    function prevPage() {
        if (vm.page < 1) { return; }
        vm.page--;
        getProducts();
    }
    function nextPage() {
        if ((vm.page + 2) > vm.pages) { return; }
        vm.page++;
        getProducts();
    }

    function sendForm() {
        appFactory.sendForm(vm.newProduct)
        .then(function(response) {
            vm.getProducts();
            vm.newProduct = {};
        })
    }

    function getCategories() {
        appFactory.getCategories()
        .then(function(categories) {
            vm.categories = categories;
            console.log('categories', categories);
        })
    }

    function getConditions() {
        appFactory.getConditions()
        .then(function(conditions) {
            vm.conditions = conditions;
            console.log('conditions', conditions);
        })
    }

    function deleteRow(product) {
        console.log('product to delete', product);
        appFactory.deleteRow(product)
        .then(function() {
            vm.getProducts();
        })
    }

    function updateRow(product) {
        console.log('product to update', product);
        appFactory.updateRow(product)
        .then(function(conditions) {
            vm.conditions = conditions;
            console.log('conditions', conditions);
            vm.getProducts();
        })
    }

    function searchForm(params) {
        console.log('search', params);
        appFactory.searchForm(params)
        .then(function(products) {
            console.log('search results:', products);
            vm.products = products;
            vm.searchProduct = {};
        })
    }

    function init() {
        vm.getPages();
        vm.getProducts();
        vm.getCategories();
        vm.getConditions();
    }

    vm.init();

}]);

myApp.factory('appFactory', ['$q', '$http', function ($q, $http) {
    return {
        getProducts: function(data) {
            var deferred = $q.defer(),
                url = 'api/Products/getProductsWithCategories';

            $http.post(url, {'data':data})
                .then(function(data) {
                    deferred.resolve(data.data);
                });

            return deferred.promise;
        },
        getPages: function(data) {
            var deferred = $q.defer(),
                url = 'api/Products/getPages';

            $http.get(url)
                .then(function(data) {
                    deferred.resolve(data.data);
                });

            return deferred.promise;
        },
        sendForm: function(data) {
            var deferred = $q.defer(),
                url = 'api/Products/saveNewProduct';

            $http.post(url, {'data': data})
                .then(function(data) {
                    console.log('data', data);
                    deferred.resolve(data.data);
                });

            return deferred.promise;
        },
        getCategories: function() {
            var deferred = $q.defer(),
                url = 'api/Categories/getCategories';

            $http.get(url)
                .then(function(data) {
                    deferred.resolve(data.data);
                });

            return deferred.promise;
        },
        getConditions: function() {
            var deferred = $q.defer(),
                url = 'api/Conditions/getConditions';

            $http.get(url)
                .then(function(data) {
                    deferred.resolve(data.data);
                });

            return deferred.promise;
        },
        deleteRow: function(data) {
            var deferred = $q.defer(),
                url = 'api/Products/deleteProduct';

            $http.post(url, {'data': data})
                .then(function(data) {
                    deferred.resolve(data.data);
                });

            return deferred.promise;
        },
        updateRow: function(data) {
            var deferred = $q.defer(),
                url = 'api/Products/updateProduct';

            $http.post(url, {'data': data})
                .then(function(data) {
                    deferred.resolve(data.data);
                });

            return deferred.promise;
        },
        searchForm: function(data) {
            var deferred = $q.defer(),
                url = 'api/Products/searchProduct';

            $http.post(url, {'data': data})
                .then(function(data) {
                    deferred.resolve(data.data);
                });

            return deferred.promise;
        }
    };
}]);
