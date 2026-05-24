# Task 01: Plan and Design Web Application

## 1. Similar Website Analysis

To plan the new "EcoSprout" website, we analyzed two simple plant nursery websites:
- **Site A (e.g., The Sill)**
  - **Design Factors**: Uses a very clean, minimalist, white and green color palette. Emphasizes high-quality images of plants.
  - **Features**: Easy navigation by categories (Indoor, Outdoor, Pet-friendly). Simple checkout process.
- **Site B (e.g., Bloomscape)**
  - **Design Factors**: Focuses on ease of use with prominent "add to cart" buttons and clear typography. Earthy tones.
  - **Features**: Includes care guides for each plant.
  
**Conclusion for EcoSprout**: We will adopt a clean white background with a vibrant "nature green" as the primary brand color to evoke freshness. The navigation will be extremely simple, focusing on "Plants", "About", and "Contact" to cater to beginners. 

## 2. UI Design & Sitemap

### Sitemap
- Home (`index.php`)
- Plants Catalog (`plants.php`)
  - Plant Details (`plant-details.php`)
- About Us (`about.php`)
- Contact (`contact.php`)
- Login/Register (`login.php`, `register.php`)
- Cart/Checkout (`cart.php`, `checkout.php`)
- Admin Dashboard (`admin/index.php`)

### Wireframe Explanations
1. **Home Page**:
   - **Header**: Simple Navigation bar with Logo (EcoSprout), Home, Plants, About, Contact, Cart, and Login links.
   - **Hero Section**: A large banner image of a greenhouse with a welcoming message and a "Shop Now" call to action button.
   - **Featured Plants Section**: A 3-column grid displaying cards of popular plants (Image, Name, Price).
   - **Footer**: Simple copyright information and quick links.

2. **Plants Catalog**:
   - A search bar at the top to filter plants.
   - A grid layout of plant cards. Each card has an image, title, price, and an "Add to Cart" or "View Details" button.

3. **Plant Details Page**:
   - Left side: Large image of the plant.
   - Right side: Title, Price, Description, Category, Care Instructions, and a quantity selector with an "Add to Cart" button.

### Design Decisions & Justifications
- **Colors**: Primary color: `#198754` (Bootstrap Success Green) to represent nature and growth. Background: `#f8f9fa` (Light grey) for a clean look.
- **Typography**: A simple, highly legible sans-serif font like Inter or Roboto (via Bootstrap defaults) will be used to ensure readability.
- **Framework**: Bootstrap 5 will be utilized because it allows rapid creation of responsive grid layouts and forms, which is essential for a simple university project. 
- **Navigation Flow**: The flow is linear. A user browses the catalog, clicks a plant to read details, adds it to the cart, and proceeds to checkout, mirroring standard intuitive e-commerce experiences.
