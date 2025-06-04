from picamera2 import Picamera2
import cv2
import face_recognition
import numpy as np
from PIL import Image, ImageDraw, ImageFont
import time
import pickle
import mysql.connector
import requests
from datetime import datetime, date
from geopy.geocoders import Nominatim
from geopy.exc import GeocoderTimedOut, GeocoderServiceError
import RPi.GPIO as GPIO  # ✅ Import GPIO module for LED control

# ✅ Set up GPIO
GPIO.setmode(GPIO.BOARD)
GPIO.setwarnings(False)

# ✅ LED Pin Definitions (Physical Pin Numbers)
RED_LED = 37     # 🔴 Red LED on Pin 37 (GPIO 26)
GREEN_LED = 35   # 🟢 Green LED on Pin 35 (GPIO 19)
ORANGE_LED = 33  # 🟠 Orange LED on Pin 33 (GPIO 13)
BLUE_LED = 31    # 🔵 Blue LED on Pin 31 (GPIO 6)

# ✅ Set LED Pins as OUTPUT
GPIO.setup(RED_LED, GPIO.OUT)
GPIO.setup(GREEN_LED, GPIO.OUT)
GPIO.setup(ORANGE_LED, GPIO.OUT)
GPIO.setup(BLUE_LED, GPIO.OUT)

# ✅ Function to turn off all LEDs
def turn_off_leds():
    GPIO.output(RED_LED, GPIO.LOW)
    GPIO.output(GREEN_LED, GPIO.LOW)
    GPIO.output(ORANGE_LED, GPIO.LOW)
    GPIO.output(BLUE_LED, GPIO.LOW)

# ✅ Turn off all LEDs initially
turn_off_leds()


# ✅ Load pre-trained face encodings
print("[INFO] Loading encodings...")
with open("encodings.pickle", "rb") as f:
    data = pickle.loads(f.read())

known_face_encodings = data["encodings"]
known_face_names = data["names"]

cv_scaler = 4

face_locations = []
face_encodings = []
face_names = []
frame_count = 0
start_time = time.time()
fps = 0

# ✅ Dictionary to track last logged time per user
last_logged_time = {}

# ✅ Initialize Picamera2
camera = Picamera2()
camera.configure(camera.create_preview_configuration(main={"format": 'XRGB8888', "size": (700, 550)}))

camera.start()

time.sleep(2)  # Allow camera to warm up

# ✅ Function to calculate age from date_of_birth
def calculate_age(birthdate):
    today = datetime.today()
    
    if isinstance(birthdate, date):
        birthdate = birthdate.strftime("%Y-%m-%d")

    birthdate = datetime.strptime(birthdate, "%Y-%m-%d")
    age = today.year - birthdate.year - ((today.month, today.day) < (birthdate.month, birthdate.day))
    
    return age

# ✅ Function to fetch user info from database (Face Recognition)
def get_user_info(username):
    try:
        conn = mysql.connector.connect(
            host="localhost",
            user="din user",
            password="din password",
            database="id_control"
        )
        cursor = conn.cursor()

        sql = """SELECT personNumber, DATE_FORMAT(date_of_birth, '%Y-%m-%d'), politi_status 
                 FROM users WHERE fullname = %s"""
        cursor.execute(sql, (username,))
        result = cursor.fetchone()

        cursor.close()
        conn.close()

        if result:
            person_number, birth_date, politi_status = result
            age = calculate_age(birth_date)

            # ✅ Fix: Ensure `politi_status` is correctly handled as NULL
            if politi_status is not None and politi_status.strip() != "":
                return person_number, age, True  # 🚨 Flag as denied by police

            return person_number, age, False  # ✅ Allowed

        return None, None, False

    except mysql.connector.Error as err:
        print(f"[ERROR] MySQL Error: {err}")
        return None, None, False


#Replace this with your actual LocationIQ API Key
LOCATIONIQ_API_KEY = "SETT_DIN_EGEN_API_NØKKEL_HER"

# Use exact GPS coordinates for better accuracy
LATITUDE = 60.153042 # 🔹 Replace with your exact latitude
LONGITUDE = 10.262770  # 🔹 Replace with your exact longitude


def get_location():
    try:
        locationiq_url = f"https://us1.locationiq.com/v1/reverse.php?key={LOCATIONIQ_API_KEY}&lat={LATITUDE}&lon={LONGITUDE}&format=json"
        headers = {"Accept-Language": "no"}
        locationiq_response = requests.get(locationiq_url, headers=headers)
        locationiq_data = locationiq_response.json()

        print("[DEBUG] LocationIQ Response:", locationiq_data)  # ✅ Print API response

        if "address" in locationiq_data:
            address = locationiq_data["address"]
            street = address.get("road", "Ukjent gate")
            house_number = address.get("house_number", "Ukjent nummer")
            city = address.get("city", address.get("town", "Ukjent by"))
            postcode = address.get("postcode", "Ukjent postnummer")
            country = address.get("country", "Ukjent land")

            full_address = f"{street} {house_number}, {postcode} {city}, {country}"
            return full_address  # ✅ Correct location

        else:
            print(f"[ERROR] LocationIQ API failed: {locationiq_data}")
            return "Latitude: {}, Longitude: {}".format(LATITUDE, LONGITUDE)  # ✅ Fallback location

    except requests.RequestException as e:
        print(f"[ERROR] API request failed: {e}")

    return "Ukjent plassering"    

# ✅ Function to insert logs including `personNumber`
# ✅ Function to insert logs including `personNumber` and `location`
def insert_log(username, person_number, logstatus):
    try:
        conn = mysql.connector.connect(
            host="localhost",
            user="group4",
            password="moh76med",
            database="id_control"
        )
        cursor = conn.cursor()

        user_location = get_location()  # ✅ Get accurate location from LocationIQ API

        sql_insert = """INSERT INTO logs (username, personNumber, logstatus, location, time_date)
                        VALUES (%s, %s, %s, %s, NOW())"""
        cursor.execute(sql_insert, (username, person_number, logstatus, user_location))
        conn.commit()

        print(f"[LOG INSERTED] {username} | {person_number} | {logstatus} | Location: {user_location}")

        cursor.close()
        conn.close()

    except mysql.connector.Error as err:
        print(f"[ERROR] MySQL Error: {err}")
def process_frame(frame):
    global face_locations, face_encodings, face_names
    turn_off_leds()  # ✅ Reset all LEDs before processing

    resized_frame = cv2.resize(frame, (0, 0), fx=(1 / cv_scaler), fy=(1 / cv_scaler))
    rgb_resized_frame = cv2.cvtColor(resized_frame, cv2.COLOR_BGR2RGB)

    face_locations = face_recognition.face_locations(rgb_resized_frame)
    face_encodings = face_recognition.face_encodings(rgb_resized_frame, face_locations)

    face_data = []  # ✅ Store name, age, and color separately
    current_time = time.time()

    # ✅ Default message when no face is detected
    if not face_encodings:
        GPIO.output(BLUE_LED, GPIO.HIGH)  # ✅ No face detected → Turn ON BLUE LED
        logstatus = "Ingen ansikt er detected"  # ✅ Log status for no face detected
        return frame, logstatus, (0, 0, 255), []  # 🔵 Blue color

    GPIO.output(BLUE_LED, GPIO.LOW)  # ✅ Face detected → Turn OFF BLUE LED

    for face_encoding in face_encodings:
        matches = face_recognition.compare_faces(known_face_encodings, face_encoding)
        name = "Personen er ikke registrert!"
        person_number = "Unknown"  # ✅ Default value
        user_age = None
        denied_by_police = False

        face_distances = face_recognition.face_distance(known_face_encodings, face_encoding)
        best_match_index = np.argmin(face_distances)
        if matches[best_match_index]:
            name = known_face_names[best_match_index]
            person_number, user_age, denied_by_police = get_user_info(name)

        # ✅ Determine LED status and status message
        if denied_by_police:
            logstatus = "Permission Denied - Politi-Merkning"
            GPIO.output(RED_LED, GPIO.HIGH)  # 🔴 Red LED ON
            status_message = "Stengt av politiet - Ingen tilgang"
            status_color = (255, 0, 0)
        elif user_age is None:
            logstatus = "Permission Denied - Not Registered"
            GPIO.output(RED_LED, GPIO.HIGH)  # 🔴 Red LED ON
            status_message = "Ikke registrert - Ingen tilgang"
            status_color = (255, 0, 0)
        elif user_age < 18:
            logstatus = "Permission Denied - Underage"
            GPIO.output(RED_LED, GPIO.HIGH)  # 🔴 Red LED ON
            status_message = "Under 18 - Ingen tilgang"
            status_color = (255, 0, 0)
        elif 18 <= user_age < 20:
            logstatus = "Limited Permission - No Spirits"
            GPIO.output(ORANGE_LED, GPIO.HIGH)  # 🟠 Orange LED ON
            status_message = "18+ Tillatt - Ingen Alkohol Over 22%"
            status_color = (255, 165, 0)
        else:
            logstatus = "Permission Granted - All Allowed"
            GPIO.output(GREEN_LED, GPIO.HIGH)  # 🟢 Green LED ON
            status_message = "Full tilgang - Alkohol & Tobakk tillatt"
            status_color = (0, 255, 0)

        # ✅ Insert Log if Not Already Logged in Last 60 Seconds
        if name not in last_logged_time or (current_time - last_logged_time[name]) > 60:
            insert_log(name, person_number, logstatus)  # ✅ Log entry
            last_logged_time[name] = current_time  # ✅ Prevent duplicate logs

        face_data.append((name, user_age, status_color))  # ✅ Store name, age, and color

    return frame, status_message, status_color, face_data


# ✅ DRAW RESULTS ON SCREEN
def draw_results(frame, status_message, status_color, face_data):
    pil_image = Image.fromarray(cv2.cvtColor(frame, cv2.COLOR_BGR2RGB))
    draw = ImageDraw.Draw(pil_image)
    
    # ✅ Load fonts
    font_path = "/usr/share/fonts/truetype/dejavu/DejaVuSans-Bold.ttf"
    status_font = ImageFont.truetype(font_path, 30)
    name_font = ImageFont.truetype(font_path, 20)  # Font for name and age

    # ✅ Draw bounding boxes and names separately
    for (top, right, bottom, left), (name, age, color) in zip(face_locations, face_data):
        top *= cv_scaler
        right *= cv_scaler
        bottom *= cv_scaler
        left *= cv_scaler

        # ✅ Draw face bounding box
        cv2.rectangle(frame, (left, top), (right, bottom), color, 8)

        # ✅ Draw user name and age BELOW the face
        text_x, text_y = left, bottom + 20
        text = f"{name} ({age} år)"
        draw.text((text_x, text_y), text, font=name_font, fill=(255, 255, 255))

    # ✅ Display status message at the top
    y0, dy = 80, 70
    for i, line in enumerate(status_message.split("\n")):
        y = y0 + i * dy
        draw.text((50, y), line, font=status_font, fill=status_color)

    return cv2.cvtColor(np.array(pil_image), cv2.COLOR_RGB2BGR)


# ✅ MAIN LOOP
try:
    while True:
        frame = camera.capture_array()
        processed_frame, status_message, status_color, face_data = process_frame(frame)
        display_frame = draw_results(processed_frame, status_message, status_color, face_data)
        cv2.imshow('Face Recognition', display_frame)

        if cv2.waitKey(1) == ord("q"):
            break

except KeyboardInterrupt:
    print("[INFO] Program interrupted by user")

finally:
    turn_off_leds()  # ✅ Turn off all LEDs before exiting
    camera.close()
    cv2.destroyAllWindows()
    GPIO.cleanup()  # ✅ Clean up GPIO resources
    print("[INFO] GPIO Cleanup Done")
