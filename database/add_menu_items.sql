-- Adds the Desserts category and the extended menu to a database created from the original schema.sql.
-- Safe to run more than once:  mysql -u root -p < database/add_menu_items.sql
USE blessed_snaks;

INSERT IGNORE INTO categories (name, slug, sort_order) VALUES ('Desserts', 'desserts', 4);

INSERT INTO products (category_id, name, description, price, image, badge)
SELECT c.id, 'Classic Cheeseburger', 'Flame-grilled beef, cheddar, lettuce, tomato and pickles on a sesame bun.', 18000, 'https://images.unsplash.com/photo-1571091718767-18b5b1457add?auto=format&fit=crop&w=600&q=70', NULL
FROM categories c WHERE c.slug = 'mains' AND NOT EXISTS (SELECT 1 FROM products WHERE name = 'Classic Cheeseburger');

INSERT INTO products (category_id, name, description, price, image, badge)
SELECT c.id, 'Smokehouse Burger & Fries', 'Smoky BBQ beef burger with crispy onions, served with a basket of golden fries.', 26000, 'https://images.unsplash.com/photo-1594212699903-ec8a3eca50f5?auto=format&fit=crop&w=600&q=70', 'New'
FROM categories c WHERE c.slug = 'mains' AND NOT EXISTS (SELECT 1 FROM products WHERE name = 'Smokehouse Burger & Fries');

INSERT INTO products (category_id, name, description, price, image, badge)
SELECT c.id, 'Crispy Chicken Burger', 'Buttermilk-fried chicken thigh, slaw and spicy mayo in a soft brioche bun.', 20000, 'https://images.unsplash.com/photo-1606755962773-d324e0a13086?auto=format&fit=crop&w=600&q=70', NULL
FROM categories c WHERE c.slug = 'mains' AND NOT EXISTS (SELECT 1 FROM products WHERE name = 'Crispy Chicken Burger');

INSERT INTO products (category_id, name, description, price, image, badge)
SELECT c.id, 'Veggie Garden Burger', 'Spiced bean and vegetable patty with avocado, lettuce and tomato. 100% meat-free.', 17000, 'https://images.unsplash.com/photo-1525059696034-4967a8e1dca2?auto=format&fit=crop&w=600&q=70', 'Veggie'
FROM categories c WHERE c.slug = 'mains' AND NOT EXISTS (SELECT 1 FROM products WHERE name = 'Veggie Garden Burger');

INSERT INTO products (category_id, name, description, price, image, badge)
SELECT c.id, 'Grilled Steak & Chips', 'Juicy sirloin grilled to order, sliced and served with seasoned chips and herb butter.', 38000, 'https://images.unsplash.com/photo-1600891964092-4316c288032e?auto=format&fit=crop&w=600&q=70', 'Chef''s Pick'
FROM categories c WHERE c.slug = 'mains' AND NOT EXISTS (SELECT 1 FROM products WHERE name = 'Grilled Steak & Chips');

INSERT INTO products (category_id, name, description, price, image, badge)
SELECT c.id, 'Spaghetti Carbonara', 'Spaghetti in a silky egg and parmesan sauce with crispy beef bacon and black pepper.', 24000, 'https://images.unsplash.com/photo-1612874742237-6526221588e3?auto=format&fit=crop&w=600&q=70', NULL
FROM categories c WHERE c.slug = 'mains' AND NOT EXISTS (SELECT 1 FROM products WHERE name = 'Spaghetti Carbonara');

INSERT INTO products (category_id, name, description, price, image, badge)
SELECT c.id, 'BBQ Chicken Pizza', 'Smoky BBQ base, grilled chicken, red onion and mozzarella, finished with fresh coriander.', 36000, 'https://images.unsplash.com/photo-1565299624946-b28f40a0ae38?auto=format&fit=crop&w=600&q=70', NULL
FROM categories c WHERE c.slug = 'mains' AND NOT EXISTS (SELECT 1 FROM products WHERE name = 'BBQ Chicken Pizza');

INSERT INTO products (category_id, name, description, price, image, badge)
SELECT c.id, 'Veggie Supreme Pizza', 'Tomato, olives, peppers, mushrooms and basil on a stretchy mozzarella base.', 32000, 'https://images.unsplash.com/photo-1585238342024-78d387f4a707?auto=format&fit=crop&w=600&q=70', 'Veggie'
FROM categories c WHERE c.slug = 'mains' AND NOT EXISTS (SELECT 1 FROM products WHERE name = 'Veggie Supreme Pizza');

INSERT INTO products (category_id, name, description, price, image, badge)
SELECT c.id, 'Beef Samosas (3 pcs)', 'Crisp golden pastry filled with spiced minced beef and onion, with a tangy chilli dip.', 6000, 'https://images.unsplash.com/photo-1601050690597-df0568f70950?auto=format&fit=crop&w=600&q=70', NULL
FROM categories c WHERE c.slug = 'starters' AND NOT EXISTS (SELECT 1 FROM products WHERE name = 'Beef Samosas (3 pcs)');

INSERT INTO products (category_id, name, description, price, image, badge)
SELECT c.id, 'Chicken Tenders', 'Five crunchy chicken strips with garlic dip. Perfect for sharing (or not).', 15000, 'https://images.unsplash.com/photo-1562967914-608f82629710?auto=format&fit=crop&w=600&q=70', NULL
FROM categories c WHERE c.slug = 'starters' AND NOT EXISTS (SELECT 1 FROM products WHERE name = 'Chicken Tenders');

INSERT INTO products (category_id, name, description, price, image, badge)
SELECT c.id, 'Toasted Club Sandwich', 'Triple-decker toast with chicken, egg, cheese and tomato, served with ketchup.', 14000, 'https://images.unsplash.com/photo-1528735602780-2552fd46c7af?auto=format&fit=crop&w=600&q=70', NULL
FROM categories c WHERE c.slug = 'starters' AND NOT EXISTS (SELECT 1 FROM products WHERE name = 'Toasted Club Sandwich');

INSERT INTO products (category_id, name, description, price, image, badge)
SELECT c.id, 'Fresh Mint Lemonade', 'Freshly squeezed lemons, crushed mint and a touch of honey over ice.', 7000, 'https://images.unsplash.com/photo-1513558161293-cdaf765ed2fd?auto=format&fit=crop&w=600&q=70', NULL
FROM categories c WHERE c.slug = 'drinks' AND NOT EXISTS (SELECT 1 FROM products WHERE name = 'Fresh Mint Lemonade');

INSERT INTO products (category_id, name, description, price, image, badge)
SELECT c.id, 'Café Latte', 'Double-shot espresso with silky steamed milk. Hot or iced.', 8000, 'https://images.unsplash.com/photo-1509042239860-f550ce710b93?auto=format&fit=crop&w=600&q=70', NULL
FROM categories c WHERE c.slug = 'drinks' AND NOT EXISTS (SELECT 1 FROM products WHERE name = 'Café Latte');

INSERT INTO products (category_id, name, description, price, image, badge)
SELECT c.id, 'Sprinkle Donuts (2 pcs)', 'Soft glazed donuts dipped in chocolate and topped with rainbow sprinkles.', 7000, 'https://images.unsplash.com/photo-1551024601-bec78aea704b?auto=format&fit=crop&w=600&q=70', NULL
FROM categories c WHERE c.slug = 'desserts' AND NOT EXISTS (SELECT 1 FROM products WHERE name = 'Sprinkle Donuts (2 pcs)');

INSERT INTO products (category_id, name, description, price, image, badge)
SELECT c.id, 'Chocolate Fudge Sundae', 'Vanilla ice cream layered with brownie, hot fudge and whipped cream.', 12000, 'https://images.unsplash.com/photo-1563805042-7684c019e1cb?auto=format&fit=crop&w=600&q=70', 'Sweet'
FROM categories c WHERE c.slug = 'desserts' AND NOT EXISTS (SELECT 1 FROM products WHERE name = 'Chocolate Fudge Sundae');

INSERT INTO products (category_id, name, description, price, image, badge)
SELECT c.id, 'Strawberry Ice Cream Cone', 'Two scoops of creamy strawberry ice cream in a crunchy waffle cone.', 6000, 'https://images.unsplash.com/photo-1497034825429-c343d7c6a68f?auto=format&fit=crop&w=600&q=70', NULL
FROM categories c WHERE c.slug = 'desserts' AND NOT EXISTS (SELECT 1 FROM products WHERE name = 'Strawberry Ice Cream Cone');
