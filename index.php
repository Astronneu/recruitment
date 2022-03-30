<!DOCTYPE html>
<head>
    <title>Prosty koszyk</title>
</head>
<body>
    <?php

    class Basket{

        /* Ta tabelka przechowuje obiekty produktów dodanych do koszyka */
        public $products_in_cart = array();
        public $products_in_cart_counter = array();

        /* Ta funkcja dodaje do tabeli z produktami obiekt produktu */
        function addToCart($productObj){ 

            /* Tutaj dodaje obiekt produktu do tabeli z obiektami aby później łatwo było dostać się do info produktu */
            global $products_in_cart, $products_in_cart_counter;
            $products_in_cart[] = $productObj;

            /* Tutaj dodaje do licznika produktów */
            if(empty($products_in_cart_counter[$productObj->productID])){
                $products_in_cart_counter[$productObj->productID] = 1;
            }
            else{
                $products_in_cart_counter[$productObj->productID] += 1;
            }
            
        }

        /* Ta funkcja usuwa produkt z koszyka o podanym ID */
        function removeFromCart($productID){
            global $products_in_cart, $products_in_cart_counter;

            /* Tutaj usuwamy obiekt z tabeli z obiektami */
            foreach($products_in_cart as $key=> $product){
                if($product->productID == $productID){
                    unset($products_in_cart[$key]);
                    break;
                }
            }

            /* Tutaj odejmuje od licznika produktów */
            if(empty($products_in_cart_counter[$productID])){
                echo "Nie posiadasz takiego produktu w koszyku";
            }
            else{
                $products_in_cart_counter[$productID] -= 1;
            }
        }

        /* Ta funkcja oblicza całkowitą wartość produktów w koszyku */
        function calculateTotals(){ 
            global $products_in_cart;
            $total = 0;

            /* Sprawdzamy tutaj czy koszyk jest pusty a jeśli nie to z każdego obiektu ściągamy cenę i dodajemy do $total */
            if(!empty($products_in_cart)){
                foreach($products_in_cart as $key=> $product){
                    $total += $product->productPrice;
                }
            }
            else return "Koszyk jest pusty!";

            return $total;
        }

        /* Ta funkcja zapisuje do pliku .txt lub .csv zależnie od podanego argumentu */
        function saveToFile($filetype){
            global $products_in_cart, $products_in_cart_counter;

            /* Zapisywanie do pliku .txt */
            if($filetype == "txt"){
                $basket_file = fopen("basket.txt", "w") or die("Z jakiegoś powodu niemożna otworzyć pliku :c");

                foreach($products_in_cart_counter as $key => $counter){
                    foreach($products_in_cart as $product_key => $product){
                        if($key == $product->productID){
                            $txt_file = "Nazwa: $product->productName (ID $product->productID) $product->productPrice | | $product->productCode x $counter \n"; 
                            fwrite($basket_file,$txt_file);
                            break;
                        }
                    }
                }
                fclose($basket_file);
            }

            /* Zapisywanie do pliku .csv */
            else if($filetype == "csv"){

                $basket_file = fopen("basket.csv", "w") or die("Z jakiegoś powodu niemożna otworzyć pliku :c");

                $basket_array = array();
                $basket_array[] = array("Nazwa","ID","Kod","Cena za sztukę","Ilość");
                foreach($products_in_cart_counter as $key => $counter){
                    foreach($products_in_cart as $product_key => $product){
                        if($key == $product->productID){
                            $basket_array[] = array($product->productName,$product->productID,$product->productCode,$product->productPrice,$counter); 
                            
                            break;
                        }
                    }
                }
                foreach($basket_array as $basket_line){
                    fputcsv($basket_file,$basket_line);
                }
                fclose($basket_file);

            }
            /* Podaje informacje, że źle podano typ pliku */
            else{
                echo "Przykro mi, nie rozpoznaje takiego typu plików... Spróbuj użyć txt lub csv ";
            }
        }
    }
    


    class Product{

        public $productID = 0;
        public $productName = "Unknown";
        public $productPrice = 0;
        public $productCode = "00000";

        function __construct($id,$name,$price,$code) {
            $this->productID = $id;
            $this->productName = $name;
            $this->productPrice = $price;
            $this->productCode = $code;
          }
    }

    /**
     * PRZYKŁAD UŻYWANIA KOSZYKA
    */


    /* Tworzymy obiekt koszyka */
    $basket = new Basket();

    /* Dodajemy produkty tworząc im obiekty (alpha,beta oraz delta) w nawiasie wpisujemy następująco 1. Id produktu, 2. Nazwa produktu, 3. Cena produktu, 4.Kod produktu */
    $product_alpha = new Product(1,"Alpha",39.99,"p1v3");
    $product_beta = new Product(2,"Beta",19.99,"3v1p");
    $product_delta = new Product(3,"Delta",2.99,"p333");
    $product_gamma = new Product(4,"Gamma",59.99,"33a3");
    $product_epsilon = new Product(5,"Epsilon",12.99,"dn41");

    /* Dodajemy stworzone produkty do koszyka */
    $basket -> addToCart($product_delta);
    $basket -> addToCart($product_alpha);
    $basket -> addToCart($product_gamma);
    $basket -> addToCart($product_alpha);
    $basket -> addToCart($product_delta);
    $basket -> addToCart($product_gamma);
    $basket -> addToCart($product_gamma);
    $basket -> addToCart($product_gamma);
    $basket -> addToCart($product_gamma);

    /* Wyświetlamy wartość koszyka korzystając z funkcji w klasie $basket */
    echo "<br/>Total: $".$basket->calculateTotals()."<br/>";   

    /* Usuwamy produkty z koszyka podając jako argument ID produktu, który chcemy usunąć */
    $basket -> removeFromCart(4);
    $basket -> addToCart($product_gamma);

    /* I ponownie wyświetlamy wartość koszyka */
    echo "<br/>Total: $".$basket->calculateTotals()."<br/>";

    $basket->saveToFile("txt");
    $basket->saveToFile("csv");


    
    
    ?>
</body>
</html>