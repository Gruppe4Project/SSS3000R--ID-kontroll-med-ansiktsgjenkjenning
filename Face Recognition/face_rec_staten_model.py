import sys
import locale
sys.stdout.reconfigure(encoding='utf-8')
import cv2
import numpy as np
from PIL import Image, ImageDraw, ImageFont
import face_recognition
import time
import pickle
import mysql.connector
from datetime import datetime
from picamera2 import Picamera2
import RPi.GPIO as GPIO  # ✅ Import GPIO module for LED control

# ✅ Set up GPIO
GPIO.setmode(GPIO.BOARD)
GPIO.setwarnings(False)

# ✅ LED Pin Definitions (Physical Pin Numbers)
RED_LED = 37     # 🔴 Red LED on Pin 37 (GPIO 26)
GREEN_LED = 35   # 🟢 Green LED on Pin 35 (GPIO 19)
BLUE_LED = 31    # 🔵 Blue LED on Pin 31 (GPIO 6)

# ✅ Set LED Pins as OUTPUT
GPIO.setup(RED_LED, GPIO.OUT)
GPIO.setup(GREEN_LED, GPIO.OUT)
GPIO.setup(BLUE_LED, GPIO.OUT)

# ✅ Function to turn off all LEDs
def turn_off_leds():
    GPIO.output(RED_LED, GPIO.LOW)
    GPIO.output(GREEN_LED, GPIO.LOW)
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
# ✅ Initialize Picamera2
camera = Picamera2()
camera.configure(camera.create_preview_configuration(main={"format": 'XRGB8888', "size": (700, 550)}))

camera.start()

time.sleep(2)
 

# ✅ Function to fetch user details from the database
def get_user_details(username):
    try:
        conn = mysql.connector.connect(
            host="localhost",
            user="din user",
            password="din password",
            database="id_control",
            charset="utf8mb4",
            collation="utf8mb4_unicode_ci"
        )
        cursor = conn.cursor(dictionary=True)

        cursor.execute("SET NAMES utf8mb4;")
        cursor.execute("SET CHARACTER SET utf8mb4;")
        cursor.execute("SET collation_connection = 'utf8mb4_unicode_ci';")

        sql = "SELECT fullname, personNumber, date_of_birth, adresse, email FROM users WHERE fullname = %s"
        cursor.execute(sql, (username,))
        result = cursor.fetchone()

        cursor.close()
        conn.close()

        if result:
            return result

        return None

    except mysql.connector.Error as err:
        print(f"[ERROR] MySQL Error: {err}")
        return None
import requests
import mysql.connector
import time

# ✅ Replace this with your actual LocationIQ API Key
LOCATIONIQ_API_KEY = "SETT_DIN_EGEN_API_NØKKEL_HER"


# ✅ Use exact GPS coordinates for better accuracy
LATITUDE = 60.153042 # 🔹 Replace with your exact latitude
LONGITUDE = 10.262770 # 🔹 Replace with your exact longitude

# ✅ Dictionary to track last logged time per user
last_logged_time = {}

# ✅ Function to fetch location using LocationIQ API
def get_location():
    try:
        locationiq_url = f"https://us1.locationiq.com/v1/reverse.php?key={LOCATIONIQ_API_KEY}&lat={LATITUDE}&lon={LONGITUDE}&format=json"
        headers = {"Accept-Language": "no"}  # ✅ Force Norwegian language
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
            return f"Latitude: {LATITUDE}, Longitude: {LONGITUDE}"  # ✅ Fallback location

    except requests.RequestException as e:
        print(f"[ERROR] API request failed: {e}")

    return "Ukjent plassering"    

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

        # ✅ Ensure `person_number` is logged as "Unknown" if not provided
        if person_number is None:
            person_number = "Unknown"

        sql_insert = """INSERT INTO logs (username, personNumber, logstatus, location, time_date)
                        VALUES (%s, %s, %s, %s, NOW())"""
        cursor.execute(sql_insert, (username, person_number, logstatus, user_location))
        conn.commit()

        print(f"[LOG INSERTED] {username} | {person_number} | {logstatus} | Location: {user_location}")

        cursor.close()
        conn.close()

    except mysql.connector.Error as err:
        print(f"[ERROR] MySQL Error: {err}")

# ✅ Function to log detections (prevents duplicate logs within 60 seconds)
def log_detection(username, person_number, logstatus):
    global last_logged_time
    current_time = time.time()

    # ✅ Prevent logging the same user within 60 seconds
    if username not in last_logged_time or (current_time - last_logged_time[username]) > 60:
        insert_log(username, person_number, logstatus)
        last_logged_time[username] = current_time

def process_frame(frame):
    global face_locations, face_encodings, face_names, last_logged_time
    turn_off_leds()  # ✅ Reset all LEDs before processing

    resized_frame = cv2.resize(frame, (0, 0), fx=(1 / cv_scaler), fy=(1 / cv_scaler))
    rgb_resized_frame = cv2.cvtColor(resized_frame, cv2.COLOR_BGR2RGB)

    face_locations = face_recognition.face_locations(rgb_resized_frame)
    face_encodings = face_recognition.face_encodings(rgb_resized_frame, face_locations)

    face_names = []  # ✅ Ensure it's always a list

    # ✅ Default: No face detected → Turn ON BLUE LED
    if not face_encodings:
        GPIO.output(BLUE_LED, GPIO.HIGH)
        return frame, "Ingen ansikt er detected!", (0, 0, 255), []

    GPIO.output(BLUE_LED, GPIO.LOW)  # ✅ Face detected → Turn OFF BLUE LED

    for face_encoding in face_encodings:
        matches = face_recognition.compare_faces(known_face_encodings, face_encoding)
        name = "Person er ikke registrert!"  # Default for unrecognized faces
        person_number = "Unknown"  # ✅ Default for unknown users

        face_distances = face_recognition.face_distance(known_face_encodings, face_encoding)
        if len(face_distances) > 0:
            best_match_index = np.argmin(face_distances)
            if matches[best_match_index]:
                name = known_face_names[best_match_index]

        user_details = get_user_details(name)
        if user_details:
            # ✅ Fetch `person_number` for logging
            person_number = user_details['personNumber']

            # ✅ Person is registered → Turn ON GREEN LED
            GPIO.output(GREEN_LED, GPIO.HIGH)
            logstatus = "Personer er detected"  # ✅ Correct log message
            status_message = (f"Navn: {user_details['fullname']}\n"
                              f"Person Nummer: {person_number}\n"
                              f"Fødselsdato: {user_details['date_of_birth']}\n"
                              f"Adresse: {user_details['adresse']}\n"
                              f"E-post: {user_details['email']}")
            box_color = (0, 255, 0)  # ✅ Green for registered users
            text_color = (255, 255, 255)  # ✅ Black text for registered users
        else:
            # ✅ Person is NOT registered → Turn ON RED LED
            GPIO.output(RED_LED, GPIO.HIGH)
            logstatus = "Person er ikke registret"  # ✅ Correct log message
            status_message = "Person er ikke registret"
            box_color = (255, 0, 0)  # ✅ Red for unregistered users
            text_color = (255, 0, 0)  # ✅ Red text for visibility

        # ✅ Log to database (Prevent duplicate logs within 60 seconds)
        current_time = time.time()
        if name not in last_logged_time or (current_time - last_logged_time[name]) > 60:
            insert_log(name, person_number, logstatus)  # ✅ FIX: Now includes `person_number`
            last_logged_time[name] = current_time

        face_names.append((name, status_message, box_color, text_color))

    return frame, status_message, box_color, face_names


# ✅ Load font for clear rendering
font_path = "/usr/share/fonts/truetype/dejavu/DejaVuSans-Bold.ttf"
font = ImageFont.truetype(font_path, 25)

# ✅ Function to draw results on screen
def draw_results(frame, status_message, status_color, face_names):
    frame_pil = Image.fromarray(cv2.cvtColor(frame, cv2.COLOR_BGR2RGB))
    draw = ImageDraw.Draw(frame_pil)

    # ✅ Default message if no face detected
    status_x, status_y = 20, 40
    if not face_names:
        draw.text((status_x, status_y), status_message, font=font, fill=status_color)
    else:
        for (name, details, box_color, text_color) in face_names:
            y_offset = status_y
            for line in details.split("\n"):
                draw.text((status_x, y_offset), line, font=font, fill=text_color)  # ✅ Now uses correct text color
                y_offset += 30  # Move to next line

    # ✅ Draw bounding box for each detected face
    for (top, right, bottom, left), (_, _, box_color, _) in zip(face_locations, face_names):
        top *= cv_scaler
        right *= cv_scaler
        bottom *= cv_scaler
        left *= cv_scaler

        draw.rectangle([left, top, right, bottom], outline=box_color, width=4)

    return cv2.cvtColor(np.array(frame_pil), cv2.COLOR_RGB2BGR)

# ✅ MAIN LOOP
try:
    while True:
        frame = camera.capture_array()
        processed_frame, status_message, status_color, face_names = process_frame(frame)
        display_frame = draw_results(processed_frame, status_message, status_color, face_names)

        cv2.imshow('Face Lookup System', display_frame)

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
