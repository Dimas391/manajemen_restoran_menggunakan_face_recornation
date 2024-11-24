import 'package:flutter/material.dart';

void main() {
  runApp(MyApp());
}

class MyApp extends StatelessWidget {
  @override
  Widget build(BuildContext context) {
    return MaterialApp(
      debugShowCheckedModeBanner: false,
      home: PemesananPage(),
    );
  }
}

class PemesananPage extends StatefulWidget {
  const PemesananPage({super.key});

  @override
  _PemesananState createState() => _PemesananState();
}

class _PemesananState extends State<PemesananPage> {
  @override
  Widget build(BuildContext context) {
    return MenuPage();
  }
}

class MenuPage extends StatefulWidget {
  const MenuPage({super.key});

  @override
  _MenuPageState createState() => _MenuPageState();
}

class _MenuPageState extends State<MenuPage> {
  final List<String> imagePaths = [
    'image/burger1.png',
    'image/burger2.png',
    'image/burger3.png',
    'image/burger4.png',
    'image/burger6.png',
    'image/burger7.png',
    'image/burger8.png',
    'image/burger9.png',
    'image/burger10.png',
    'image/burger11.png',
    'image/chicken/chicken1.jpeg',
    'image/chicken/chicken2.jpeg',
    'image/chicken/chicken3.jpeg',
    'image/chicken/chicken4.jpeg',
    'image/chicken/chicken5.jpeg',
    'image/chicken/chicken6.jpeg',
    'image/chicken/chicken7.jpeg',
    'image/chicken/chicken8.jpeg',
    'image/chicken/chicken9.jpeg',
    'image/chicken/chicken10.jpeg',
    'image/beef/beef1.png',
    'image/beef/beef2.png',
    'image/beef/beef3.png',
    'image/beef/beef4.png',
    'image/beef/beef5.png',
    'image/beef/beef6.png',
    'image/beef/beef7.png',
    'image/beef/beef8.png',
    'image/beef/beef9.png',
    'image/beef/beef10.png',
    'image/dessert/dessert1.png',
    'image/dessert/dessert2.png',
    'image/dessert/dessert3.png',
    'image/dessert/dessert4.png',
    'image/dessert/dessert5.png',
    'image/dessert/dessert6.png',
    'image/dessert/dessert7.png',
    'image/dessert/dessert8.png',
    'image/dessert/dessert9.png',
    'image/dessert/dessert10.png',
    'image/veg/veg1.jpg',
    'image/veg/veg2.jpg',
    'image/veg/veg3.jpg',
    'image/veg/veg4.jpg',
    'image/veg/veg5.jpg',
    'image/veg/veg6.jpg',
    'image/veg/veg7.jpg',
    'image/veg/veg8.jpg',
    'image/veg/veg9.jpg',
    'image/veg/veg10.jpg',
    'image/drink/drink1.png',
    'image/drink/drink2.png',
    'image/drink/drink3.png',
    'image/drink/drink4.png',
    'image/drink/drink5.png',
    'image/drink/drink6.png',
    'image/drink/drink7.png',
    'image/drink/drink8.png',
    'image/drink/drink9.png',
    'image/drink/drink10.png',
  ];

  final List<String> prices = [
    'Rp 25.000+',
    'Rp 30.000+',
    'Rp 28.000+',
    'Rp 35.000+',
    'Rp 20.000+',
    'Rp 18.000+',
    'Rp 25.000+',
    'Rp 37.000+',
    'Rp 47.000+',
    'Rp 46.000+',
    'Rp 30.000+',
    'Rp 40.000+',
    'Rp 35.000+',
    'Rp 40.000+',
    'Rp 25.000+',
    'Rp 20.000+',
    'Rp 20.000+',
    'Rp 35.000+',
    'Rp 30.000+',
    'Rp 20.000+',
    'Rp 15.000+',
    'Rp 19.000+',
    'Rp 15.000+',
    'Rp 16.000+',
    'Rp 25.000+',
    'Rp 20.000+',
    'Rp 30.000+',
    'Rp 20.000+',
    'Rp 30.000+',
    'Rp 15.000+',
    'Rp 40.000+',
    'Rp 35.000+',
    'Rp 25.000+',
    'Rp 37.000+',
    'Rp 45.000+',
    'Rp 30.000+',
    'Rp 35.000+',
    'Rp 40.000+',
    'Rp 30.000+',
    'Rp 25.000+',
    'Rp 15.000+',
    'Rp 10.000+',
    'Rp 10.000+',
    'Rp 17.000+',
    'Rp 8.000+',
    'Rp 12.000+',
    'Rp 9.000+',
    'Rp 10.000+',
    'Rp 10.000+',
    'Rp 25.000+',
    'Rp 20.000+',
    'Rp 19.000+',
    'Rp 25.000+',
    'Rp 25.000+',
    'Rp 30.000+',
    'Rp 25.000+',
    'Rp 20.000+',
    'Rp 15.000+',
    'Rp 18.000+',
    'Rp 19.000+',
  ];

  final List<String> names = [
    'Chicken Burger',
    'Beef Burger',
    'Smash Burger',
    'Classic Burger',
    'Lentil Burger',
    'Black Bean Burger',
    'Beet Burger (Vegan)',
    'Chili Burger',
    'Tuna Fish Burger',
    'Salmon Fish Burger',
    'Chicken Florentine',
    'Lemon Chicken',
    'SChicken Shawarma',
    'Lemon Chicken',
    'Chicken Katsu',
    'Oven Fried Chicken',
    'Coronation Chicken',
    'Chicken Adobo',
    'Chicken Tinga',
    'Keto Med Chicken',
    'Tumis beef slice',
    'Beef slice BBQ',
    'Beef slice balado',
    'Tumis beef slice',
    'Beef slice teriyaki',
    'Gyudon',
    'Beef yakiniku',
    'Tumis beef slice',
    'Beef sliece enoki',
    'Beef slice sambal',
    'Cake',
    'Cookies',
    'Biskuit',
    'Pie',
    'Tart',
    'Pastry',
    'Brownies',
    'Custard',
    'Pudding',
    'Ice Cream',
    'Sapo tahu',
    'Gado-gado',
    'Ketoprak',
    'Sup krim jagung',
    'Sate jamur',
    'Pepes tahu',
    'Opor tahu',
    'Plecing kangkung',
    'Pecel sayur',
    'Salad buah',
    'Sugar strawberry',
    'Dalgona coffe',
    'Thai tea',
    'Pina colada',
    'Blackberry Virgin Mojito',
    'Strawberry susu',
    'Infused water',
    'Watermelon Lemonade',
    'Mango Lemonade',
    'Golden Latte',
  ];

  final List<String> categories = [
    'All',
    'Chicken',
    'Beef',
    'Veg',
    'Dessert',
    'Drink',
  ];

  final List<bool> selectedCategories = [
    true,
    false,
    false,
    false,
    false,
    false
  ];

  List<int> displayedMenuIndices = [];

  @override
  void initState() {
    super.initState();
    // Menampilkan semua menu secara default
    displayedMenuIndices = List.generate(imagePaths.length, (index) => index);
  }

  void filterMenu(int index) {
    setState(() {
      displayedMenuIndices.clear();
      if (index == 0) {
        // Kategori All
        displayedMenuIndices = List.generate(imagePaths.length, (i) => i);
      } else if (index == 1) {
        // Kategori Chicken
        displayedMenuIndices = [
          0,
          10,
          11,
          12,
          13,
          14,
          15,
          16,
          17,
          18,
          19
        ]; // Menampilkan Chicken Burger dan Chicken Florentine Style
      } else if (index == 2) {
        // Kategori Beef
        displayedMenuIndices = [
          1,
          20,
          21,
          22,
          23,
          24,
          25,
          26,
          27,
          28,
          29
        ]; // Menampilkan Beef Burger
      } else if (index == 3) {
        // Kategori Veg
        displayedMenuIndices = [
          40,
          41,
          42,
          43,
          44,
          45,
          46,
          47,
          48,
          49
        ]; // Menampilkan Lentil, Black Bean, Beet, Chili Burger
      } else if (index == 4) {
        // Kategori Dessert
        displayedMenuIndices = [
          30,
          31,
          32,
          33,
          34,
          35,
          36,
          37,
          38,
          39
        ]; // Kosongkan jika tidak ada menu dessert
      } else if (index == 5) {
        displayedMenuIndices = [50, 51, 52, 53, 54, 55, 56, 57, 58, 59];
      }

      // Reset kategori yang dipilih
      for (int i = 0; i < selectedCategories.length; i++) {
        selectedCategories[i] = i == index;
      }
    });
  }

  @override
  Widget build(BuildContext context) {
    return Scaffold(
      backgroundColor: Colors.white,
      appBar: PreferredSize(
        preferredSize: const Size.fromHeight(150),
        child: AppBar(
          backgroundColor: Colors.transparent,
          elevation: 0,
          flexibleSpace: Container(
            decoration: const BoxDecoration(
              image: DecorationImage(
                image: AssetImage('image/burger5.jpg'),
                fit: BoxFit.cover,
              ),
            ),
          ),
          leading: Container(
            margin: const EdgeInsets.all(8),
            decoration: BoxDecoration(
              color: const Color.fromARGB(255, 88, 87, 87).withOpacity(0.5),
              shape: BoxShape.circle,
            ),
            child: IconButton(
              icon: const Icon(Icons.arrow_back, color: Colors.white),
              onPressed: () {
                Navigator.pop(context);
              },
            ),
          ),
        ),
      ),
      body: Padding(
        padding: const EdgeInsets.symmetric(horizontal: 16.0),
        child: Column(
          crossAxisAlignment: CrossAxisAlignment.start,
          children: [
            const SizedBox(height: 16),
            const Text(
              'Menu',
              style: TextStyle(
                fontSize: 18,
                fontWeight: FontWeight.bold,
                color: Colors.black,
              ),
            ),
            const SizedBox(height: 12),
            SingleChildScrollView(
              scrollDirection: Axis.horizontal,
              child: Row(
                children: [
                  for (int i = 0; i < categories.length; i++)
                    categoryChip(categories[i], i),
                ],
              ),
            ),
            const SizedBox(height: 12),
            Expanded(
              child: GridView.builder(
                padding: const EdgeInsets.only(bottom: 16),
                gridDelegate: const SliverGridDelegateWithFixedCrossAxisCount(
                  crossAxisCount: 2,
                  crossAxisSpacing: 12,
                  mainAxisSpacing: 12,
                  childAspectRatio: 0.8,
                ),
                itemCount: displayedMenuIndices.length,
                itemBuilder: (context, index) {
                  int itemIndex = displayedMenuIndices[index];
                  return MenuCard(
                    imagePath: imagePaths[itemIndex],
                    name: names[itemIndex],
                    price: prices[itemIndex],
                    description: 'Delicious and juicy',
                  );
                },
              ),
            ),
          ],
        ),
      ),
    );
  }

  Widget categoryChip(String label, int index) {
    return Padding(
      padding: const EdgeInsets.symmetric(horizontal: 8),
      child: ElevatedButton(
        style: ElevatedButton.styleFrom(
          foregroundColor:
              selectedCategories[index] ? Colors.white : Colors.black,
          backgroundColor: selectedCategories[index]
              ? const Color(0xFF8B42F0)
              : const Color(0xFFE0E0E0),
          shape: RoundedRectangleBorder(
            borderRadius: BorderRadius.circular(20),
          ),
        ),
        onPressed: () {
          filterMenu(index);
        },
        child: Text(label),
      ),
    );
  }
}

class MenuCard extends StatelessWidget {
  final String imagePath;
  final String name;
  final String price;
  final String description;

  const MenuCard({
    super.key,
    required this.imagePath,
    required this.name,
    required this.price,
    required this.description,
  });

  @override
  Widget build(BuildContext context) {
    return Container(
      padding: const EdgeInsets.all(8),
      decoration: BoxDecoration(
        color: const Color(0xFF302F3C),
        borderRadius: BorderRadius.circular(20),
        boxShadow: [
          BoxShadow(
            color: Colors.grey.withOpacity(0.4),
            spreadRadius: 3,
            blurRadius: 7,
            offset: const Offset(0, 3),
          ),
        ],
      ),
      child: Column(
        crossAxisAlignment: CrossAxisAlignment.center,
        children: [
          Expanded(
            flex: 6,
            child: ClipRRect(
              borderRadius: BorderRadius.circular(15),
              child: Image.asset(
                imagePath,
                fit: BoxFit.cover,
              ),
            ),
          ),
          const SizedBox(height: 10),
          Expanded(
            flex: 2,
            child: Text(
              name,
              style: const TextStyle(
                fontSize: 18,
                fontWeight: FontWeight.bold,
                color: Colors.white,
              ),
              textAlign: TextAlign.center,
            ),
          ),
          Expanded(
            flex: 1,
            child: Text(
              description,
              style: const TextStyle(
                fontSize: 14,
                color: Colors.white70,
              ),
              textAlign: TextAlign.center,
            ),
          ),
          const SizedBox(height: 10),
          Expanded(
            flex: 2,
            child: Container(
              padding: const EdgeInsets.symmetric(horizontal: 12, vertical: 6),
              decoration: BoxDecoration(
                color: Colors.white,
                borderRadius: BorderRadius.circular(12),
              ),
              child: Text(
                price,
                style: const TextStyle(
                  fontSize: 16,
                  fontWeight: FontWeight.bold,
                  color: Colors.black,
                ),
              ),
            ),
          ),
        ],
      ),
    );
  }
}
