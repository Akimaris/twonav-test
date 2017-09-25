$(document).ready(function () {
    Pizza.init();
});

var Pizza = {
    init: function () {
        this.calculatePizzaPrice();
        this.addIngredients();
        this.removeIngredients();
        this.resetIngredientsSorting();
        this.handleSortingButtons();
        this.handleSortings();
    },
    calculatePizzaPrice: function () {
        var ingredients = [];

        $('#added-ingredients').children().each(function () {
            ingredients.push($(this).attr('data-id'));
        });

        Pizza.getPizzaPrice(ingredients);
    },
    getPizzaPrice: function (ingredients) {
        $.ajax({
            url: '/admin/pizza/getPizzaPrice',
            data: {ingredients: ingredients}
        }).done(function (response) {
            $('.pizza-price').val(response.price);
        });
    },
    addIngredients: function () {
        var addIngredientButtons = $('.add-ingredient');
        addIngredientButtons.unbind('click');
        addIngredientButtons.on('click', function (e) {
            e.preventDefault();
            var _this = $(this);
            var _parent = _this.parents('.ingredient-container');

            _parent.appendTo('#added-ingredients');
            _this.addClass('remove-ingredient');
            _this.removeClass('add-ingredient');
            _this.removeClass('btn-success').addClass('btn-danger');
            _this.find(".glyphicon-plus").removeClass('glyphicon-plus').addClass('glyphicon-minus');

            Pizza.resetIngredientsSorting();
            Pizza.removeIngredients();
            Pizza.handleSortingButtons();
            Pizza.handleSortings();
            Pizza.calculatePizzaPrice();
        });
    },
    removeIngredients: function () {
        var removeIngredientButtons = $('.remove-ingredient');
        removeIngredientButtons.unbind('click');
        removeIngredientButtons.on('click', function (e) {
            e.preventDefault();
            var _this = $(this);
            var _parent = _this.parents('.ingredient-container');

            _parent.appendTo('#available-ingredients');
            _this.addClass('add-ingredient');
            _this.removeClass('remove-ingredient');
            _this.removeClass('btn-danger').addClass('btn-success');
            _this.find(".glyphicon-minus").removeClass('glyphicon-minus').addClass('glyphicon-plus');

            _parent.find('.ingredient-sorting').html("");
            _parent.find('.ingredient-form').html("");
            Pizza.addIngredients();
            Pizza.resetIngredientsSorting();
            Pizza.handleSortingButtons();
            Pizza.handleSortings();
            Pizza.calculatePizzaPrice();
        });
    },
    handleSortingButtons: function () {
        var addedIngredientsContainer = $('#added-ingredients');
        var ingredientCount = addedIngredientsContainer.children().length;

        addedIngredientsContainer.children().each(function () {
            var _this = $(this);
            var ingredientSortingContainer = _this.find('.ingredient-sorting');
            ingredientSortingContainer.html("");

            if (_this.attr('data-sorting') != 1) {
                ingredientSortingContainer.append('<a href="#" class="btn-up btn btn-primary margin-right-10"> <span class="glyphicon glyphicon-arrow-up" aria-hidden="true"></span></a>');
            }

            if (_this.attr('data-sorting') != ingredientCount) {
                ingredientSortingContainer.append('<a href="#" class="btn-down btn btn-primary"> <span class="glyphicon glyphicon-arrow-down" aria-hidden="true"></span></a>');
            }
        });
    },
    handleSortings: function () {
        Pizza.handleMoveUp();
        Pizza.handleMoveDown();
    },
    handleMoveUp: function () {
        var buttonUp = $('.btn-up');
        buttonUp.unbind('click');
        buttonUp.on('click', function (e) {
            e.preventDefault();
            var _this = $(this);
            var ingredientContainer = _this.parents('.ingredient-container');
            ingredientContainer.insertBefore(ingredientContainer.prev());

            Pizza.resetIngredientsSorting();
            Pizza.handleSortingButtons();
            Pizza.handleSortings();
        });
    },
    handleMoveDown: function () {
        var buttonDown = $('.btn-down');
        buttonDown.unbind('click');
        buttonDown.on('click', function (e) {
            e.preventDefault();
            var _this = $(this);
            var ingredientContainer = _this.parents('.ingredient-container');
            ingredientContainer.insertAfter(ingredientContainer.next());

            Pizza.resetIngredientsSorting();
            Pizza.handleSortingButtons();
            Pizza.handleSortings();
        });
    },
    resetIngredientsSorting: function () {
        var addedIngredientsContainer = $('#added-ingredients');
        var i = 1;

        addedIngredientsContainer.children().each(function () {
            $(this).attr('data-sorting', i);
            Pizza.generateHiddenInputs($(this), i);
            i++;
        });
    },
    generateHiddenInputs: function (element, sorting) {
        var ingredientForm = element.find('.ingredient-form');
        ingredientForm.html("");
        ingredientForm.append(
            '<input type="hidden" name="ingredients[' + sorting + '][id]" value="' + element.attr('data-id') + '">' +
            '<input type="hidden" name="ingredients[' + sorting + '][sorting]" value="' + sorting + '">'
        );
    }

};