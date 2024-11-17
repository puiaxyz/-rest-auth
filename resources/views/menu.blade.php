@extends('layouts.app')

@section('content')
    <h1>Menu</h1>

    <div id="menu-items" class="menu-items-container"></div>


    <script>
        
      //Configure axios to include credentials (session cookies)
axios.defaults.withCredentials = true;
  // Fetch menu items from the API
        axios.get('/api/menu-items')
            .then(response => {
                const menuItems = response.data;
                const menuDiv = document.getElementById('menu-items');
                menuDiv.innerHTML = ''; // Clear any existing content

                // Loop through each item and display it
                menuItems.forEach(item => {
                    menuDiv.innerHTML += `
                        <div class="menu-item">
                            <h4>${item.name} - $${item.price}</h4>
                            <p>${item.description}</p>
                            <div>
                                <label for="quantity-${item.id}">Quantity:</label>
                                <input type="number" min="1" value="1" id="quantity-${item.id}">
                            </div>
                            <button onclick="addToCart(${item.id})" class="btn btn-primary">Add to Cart</button>
                        </div>
                        <hr>
                    `;
                });
            })
            .catch(error => console.error('Error fetching menu items:', error));

        // Function to add items to the cart
        function addToCart(id) {
    // You can check if the user is authenticated by hitting an endpoint like '/api/user' (session-based check
                const quantity = document.getElementById(`quantity-${id}`).value;
                axios.post('/api/cart', { menu_item_id: id, quantity: quantity })
                    .then(response => {
                        alert(response.data.message);
                    })
                    .catch(error => console.error('Error adding to cart:', error));
            } 
        
       




    </script>
@endsection
