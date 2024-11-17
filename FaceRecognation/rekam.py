import cv2, time

camera = 0  # Coba ubah ke 1 atau 2 jika 0 tidak berfungsi
# Membuka webcam
video = cv2.VideoCapture(camera, cv2.CAP_DSHOW)

# Memeriksa apakah kamera berhasil dibuka
if not video.isOpened():
    print("Tidak bisa membuka kamera")
    exit()

# Algoritma deteksi wajah
faceDeteksi = cv2.CascadeClassifier(cv2.data.haarcascades + 'haarcascade_frontalface_default.xml')

# Mengambil id pengguna
id = input('Id : ')
a = 0

while True:
    a += 1
    check, frame = video.read()
    
    # Memeriksa apakah frame berhasil dibaca
    if not check:
        print("Gagal membaca frame dari kamera")
        break

    # Membuat mode pengambilan gambar pada scan menjadi Gray (abu-abu)
    abu = cv2.cvtColor(frame, cv2.COLOR_BGR2GRAY)

    # Mendeteksi wajah
    wajah = faceDeteksi.detectMultiScale(abu, 1.3, 5)
    print(wajah)

    for (x, y, w, h) in wajah:
        # Membuat file foto ke folder Dataset/ dengan identifikasi Id dan perulangan a
        cv2.imwrite('Dataset/User.' + str(id) + '.' + str(a) + '.jpg', abu[y:y+h, x:x+w])

        # Mengenali bentuk wajah (kotak warna hijau di wajah)
        cv2.rectangle(frame, (x, y), (x+w, y+h), (0, 255, 0), 2)

    # Nama Window
    cv2.imshow("Face Recognition Window", frame)

    # Perulangan dilakukan hingga 30 pengambilan foto
    if a > 29:
        break

    # Menghentikan program jika tombol 'q' ditekan
    if cv2.waitKey(1) & 0xFF == ord('q'):
        break

# Cam berhenti
video.release()
cv2.destroyAllWindows()
