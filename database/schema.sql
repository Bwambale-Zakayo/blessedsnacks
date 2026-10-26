-- Blessed Snaks database. Import with:  mysql -u root -p < schema.sql
CREATE DATABASE IF NOT EXISTS blessed_snaks CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE blessed_snaks;

CREATE TABLE IF NOT EXISTS users (
  id            INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  name          VARCHAR(80)  NOT NULL,
  email         VARCHAR(150) NOT NULL UNIQUE,
  phone         VARCHAR(20)  NOT NULL,
  password_hash VARCHAR(255) NOT NULL,
  created_at    TIMESTAMP    NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB;

CREATE TABLE IF NOT EXISTS categories (
  id         INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  name       VARCHAR(60) NOT NULL,
  slug       VARCHAR(60) NOT NULL UNIQUE,
  sort_order INT NOT NULL DEFAULT 0
) ENGINE=InnoDB;

CREATE TABLE IF NOT EXISTS products (
  id          INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  category_id INT UNSIGNED NOT NULL,
  name        VARCHAR(120) NOT NULL,
  description VARCHAR(300) NOT NULL DEFAULT '',
  price       DECIMAL(10,0) NOT NULL,
  image       VARCHAR(300) NOT NULL DEFAULT '',
  badge       VARCHAR(30)  NULL,
  is_active   TINYINT(1)   NOT NULL DEFAULT 1,
  created_at  TIMESTAMP    NOT NULL DEFAULT CURRENT_TIMESTAMP,
  FOREIGN KEY (category_id) REFERENCES categories(id)
) ENGINE=InnoDB;

CREATE TABLE IF NOT EXISTS orders (
  id             INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  user_id        INT UNSIGNED NOT NULL,
  subtotal       DECIMAL(10,0) NOT NULL,
  delivery_fee   DECIMAL(10,0) NOT NULL DEFAULT 0,
  total          DECIMAL(10,0) NOT NULL,
  status         ENUM('pending','preparing','out_for_delivery','delivered','cancelled') NOT NULL DEFAULT 'pending',
  address        VARCHAR(255) NOT NULL,
  phone          VARCHAR(20)  NOT NULL,
  payment_method ENUM('cash','mtn_momo','airtel_money') NOT NULL DEFAULT 'cash',
  created_at     TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
  FOREIGN KEY (user_id) REFERENCES users(id)
) ENGINE=InnoDB;

CREATE TABLE IF NOT EXISTS order_items (
  id         INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  order_id   INT UNSIGNED NOT NULL,
  product_id INT UNSIGNED NOT NULL,
  name       VARCHAR(120) NOT NULL,
  unit_price DECIMAL(10,0) NOT NULL,
  qty        SMALLINT UNSIGNED NOT NULL,
  FOREIGN KEY (order_id)   REFERENCES orders(id) ON DELETE CASCADE,
  FOREIGN KEY (product_id) REFERENCES products(id)
) ENGINE=InnoDB;

-- Sample data
INSERT INTO categories (name, slug, sort_order) VALUES
  ('Starters & Snacks', 'starters', 1),
  ('Mains',             'mains',    2),
  ('Drinks',            'drinks',   3),
  ('Desserts',          'desserts', 4);

INSERT INTO products (category_id, name, description, price, image, badge) VALUES
  (2, 'Blessed Signature Burger', 'Double beef patty, melted cheddar, caramelised onions and house sauce in a toasted brioche bun.', 22000, 'https://images.unsplash.com/photo-1568901346375-23c9450c58cd?auto=format&fit=crop&w=600&q=70', 'Best Seller'),
  (1, 'Golden Crispy Fries',      'Thick-cut potatoes fried to perfection, lightly salted with a choice of dip.',                     8000,  'https://images.unsplash.com/photo-1573080496219-bb080dd4f877?auto=format&fit=crop&w=600&q=70', NULL),
  (1, 'Spicy Chicken Wings',      'Six wings glazed in our smoky chilli-honey sauce, served with a cooling dip.',                    18000, 'https://images.unsplash.com/photo-1567620832903-9fc6debc209f?auto=format&fit=crop&w=600&q=70', 'Hot'),
  (2, 'Cheesy Pepperoni Pizza',   'Stone-baked 10" pizza with stretchy mozzarella, spicy pepperoni and fresh basil.',                35000, 'https://images.unsplash.com/photo-1513104890138-7c749659a591?auto=format&fit=crop&w=600&q=70', NULL),
  (2, 'Crispy Fried Chicken Meal','Two pieces of seasoned fried chicken with fries, coleslaw and a soft drink.',                     28000, 'https://images.unsplash.com/photo-1626645738196-c2a7c87a8f58?auto=format&fit=crop&w=600&q=70', NULL),
  (2, 'Creamy Chicken Pasta',     'Penne tossed in garlic cream sauce with grilled chicken and parmesan.',                           25000, 'https://images.unsplash.com/photo-1621996346565-e3dbc646d9a9?auto=format&fit=crop&w=600&q=70', NULL),
  (3, 'Fresh Fruit Smoothie',     'Blended passion fruit, mango and banana. Cold, thick and refreshing.',                            9000,  'https://images.unsplash.com/photo-1553530666-ba11a7da3888?auto=format&fit=crop&w=600&q=70', NULL),
  (3, 'Creamy Milkshake',         'Thick vanilla, chocolate or strawberry shake topped with whipped cream.',                         12000, 'https://images.unsplash.com/photo-1572490122747-3968b75cc699?auto=format&fit=crop&w=600&q=70', NULL),
  (2, 'Classic Cheeseburger',     'Flame-grilled beef, cheddar, lettuce, tomato and pickles on a sesame bun.',                      18000, 'https://images.unsplash.com/photo-1571091718767-18b5b1457add?auto=format&fit=crop&w=600&q=70', NULL),
  (2, 'Smokehouse Burger & Fries','Smoky BBQ beef burger with crispy onions, served with a basket of golden fries.',                26000, 'https://images.unsplash.com/photo-1594212699903-ec8a3eca50f5?auto=format&fit=crop&w=600&q=70', 'New'),
  (2, 'Crispy Chicken Burger',    'Buttermilk-fried chicken thigh, slaw and spicy mayo in a soft brioche bun.',                    20000, 'https://images.unsplash.com/photo-1606755962773-d324e0a13086?auto=format&fit=crop&w=600&q=70', NULL),
  (2, 'Veggie Garden Burger',     'Spiced bean and vegetable patty with avocado, lettuce and tomato. 100% meat-free.',              17000, 'https://images.unsplash.com/photo-1525059696034-4967a8e1dca2?auto=format&fit=crop&w=600&q=70', 'Veggie'),
  (2, 'Grilled Steak & Chips',    'Juicy sirloin grilled to order, sliced and served with seasoned chips and herb butter.',        38000, 'https://images.unsplash.com/photo-1600891964092-4316c288032e?auto=format&fit=crop&w=600&q=70', 'Chef''s Pick'),
  (2, 'Spaghetti Carbonara',      'Spaghetti in a silky egg and parmesan sauce with crispy beef bacon and black pepper.',          24000, 'https://images.unsplash.com/photo-1612874742237-6526221588e3?auto=format&fit=crop&w=600&q=70', NULL),
  (2, 'BBQ Chicken Pizza',        'Smoky BBQ base, grilled chicken, red onion and mozzarella, finished with fresh coriander.',     36000, 'https://images.unsplash.com/photo-1565299624946-b28f40a0ae38?auto=format&fit=crop&w=600&q=70', NULL),
  (2, 'Veggie Supreme Pizza',     'Tomato, olives, peppers, mushrooms and basil on a stretchy mozzarella base.',                   32000, 'https://images.unsplash.com/photo-1585238342024-78d387f4a707?auto=format&fit=crop&w=600&q=70', 'Veggie'),
  (1, 'Beef Samosas (3 pcs)',     'Crisp golden pastry filled with spiced minced beef and onion, with a tangy chilli dip.',         6000,  'https://images.unsplash.com/photo-1601050690597-df0568f70950?auto=format&fit=crop&w=600&q=70', NULL),
  (1, 'Chicken Tenders',          'Five crunchy chicken strips with garlic dip. Perfect for sharing (or not).',                    15000, 'https://images.unsplash.com/photo-1562967914-608f82629710?auto=format&fit=crop&w=600&q=70', NULL),
  (1, 'Toasted Club Sandwich',    'Triple-decker toast with chicken, egg, cheese and tomato, served with ketchup.',                14000, 'https://images.unsplash.com/photo-1528735602780-2552fd46c7af?auto=format&fit=crop&w=600&q=70', NULL),
  (3, 'Fresh Mint Lemonade',      'Freshly squeezed lemons, crushed mint and a touch of honey over ice.',                          7000,  'https://images.unsplash.com/photo-1513558161293-cdaf765ed2fd?auto=format&fit=crop&w=600&q=70', NULL),
  (3, 'Café Latte',               'Double-shot espresso with silky steamed milk. Hot or iced.',                                    8000,  'https://images.unsplash.com/photo-1509042239860-f550ce710b93?auto=format&fit=crop&w=600&q=70', NULL),
  (4, 'Sprinkle Donuts (2 pcs)',  'Soft glazed donuts dipped in chocolate and topped with rainbow sprinkles.',                     7000,  'https://images.unsplash.com/photo-1551024601-bec78aea704b?auto=format&fit=crop&w=600&q=70', NULL),
  (4, 'Chocolate Fudge Sundae',   'Vanilla ice cream layered with brownie, hot fudge and whipped cream.',                          12000, 'https://images.unsplash.com/photo-1563805042-7684c019e1cb?auto=format&fit=crop&w=600&q=70', 'Sweet'),
  (4, 'Strawberry Ice Cream Cone','Two scoops of creamy strawberry ice cream in a crunchy waffle cone.',                          6000,  'https://images.unsplash.com/photo-1497034825429-c343d7c6a68f?auto=format&fit=crop&w=600&q=70', NULL);
