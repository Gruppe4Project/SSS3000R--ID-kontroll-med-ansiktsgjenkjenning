from picamera2 import Picamera2
import cv2
import time
from pyzbar.pyzbar import decode
import mysql.connector
from datetime import datetime, date
import numpy as np
from PIL import Image, ImageDraw, ImageFont
import requests
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


# ✅ Database Connection Details
DB_HOST = "localhost"
DB_USER = "din_user"
DB_PASSWORD = "din_password"
DB_NAME = "id_control"

cv_scaler = 4  # ✅ Keep the same scaling factor as face recognition
last_logged_time = {}  # ✅ Track last log time per user

# ✅ Initialize Picamera2
camera = Picamera2()
camera.configure(camera.create_preview_configuration(main={"format": 'XRGB8888', "size": (700, 550)}))

camera.start()

time.sleep(2)  # ✅ Allow camera to warm up

# ✅ Function to calculate age from date_of_birth
def calculate_age(birthdate):
    today = datetime.today()
    if isinstance(birthdate, date):
        birthdate = birthdate.strftime("%Y-%m-%d")
    
    birthdate = datetime.strptime(birthdate, "%Y-%m-%d")
    age = today.year - birthdate.year - ((today.month, today.day) < (birthdate.month, birthdate.day))
    
    return age

# ✅ Function to check person in the database (QR)
def check_person_in_database(qr_data):
    try:
        connection = mysql.connector.connect(
            host=DB_HOST,
            user=DB_USER,
            password=DB_PASSWORD,
            database=DB_NAME
        )
        cursor = connection.cursor()

        sql = """SELECT fullname, personNumber, DATE_FORMAT(date_of_birth, '%Y-%m-%d'), politi_status
                 FROM users WHERE personNumber = %s"""
        cursor.execute(sql, (qr_data,))
        result = cursor.fetchone()

        cursor.close()
        connection.close()

        if result:
            fullname, person_number, birth_date, politi_status = result
            age = calculate_age(birth_date)

            # ✅ Fix: Ensure `politi_status` is correctly handled as NULL
            if politi_status is not None and politi_status.strip() != "":
                return fullname, person_number, age, True  # 🚨 Flag as denied by police

            return fullname, person_number, age, False  # ✅ Allowed

        return None, None, None, False

    except mysql.connector.Error as err:
        print(f"[ERROR] MySQL Error: {err}")
        return None, None, None, False


# ✅ LocationIQ API Configuration
LOCATIONIQ_API_KEY = "SETT_DIN_EGEN_API_NØKKEL_HER"

LATITUDE = 60.153042
LONGITUDE = 10.262770

# ✅ Function to fetch user location
def get_location():
    try:
        locationiq_url = f"https://us1.locationiq.com/v1/reverse.php?key={LOCATIONIQ_API_KEY}&lat={LATITUDE}&lon={LONGITUDE}&format=json"
        headers = {"Accept-Language": "no"}  # Force Norwegian language
        locationiq_response = requests.get(locationiq_url, headers=headers)
        locationiq_data = locationiq_response.json()

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
            return "Latitude: {}, Longitude: {}".format(LATITUDE, LONGITUDE)  # ✅ Fallback location

    except requests.RequestException as e:
        print(f"[ERROR] API request failed: {e}")

    return "Ukjent plassering"

# ✅ Function to insert logs with `personNumber` and `location`
def insert_log(username, person_number, logstatus):
    try:
        conn = mysql.connector.connect(
            host=DB_HOST,
            user=DB_USER,
            password=DB_PASSWORD,
            database=DB_NAME
        )
        cursor = conn.cursor()

        user_location = get_location()  # ✅ Get accurate location

        sql_insert = """INSERT INTO logs (username, personNumber, logstatus, location, time_date)
                        VALUES (%s, %s, %s, %s, NOW())"""
        cursor.execute(sql_insert, (username, person_number, logstatus, user_location))
        conn.commit()

        print(f"[LOG INSERTED] {username} | {person_number} | {logstatus} | Location: {user_location}")

        cursor.close()
        conn.close()

    except mysql.connector.Error as err:
        print(f"[ERROR] MySQL Error: {err}")

def process_qr_frame(frame):
    global last_logged_time
    turn_off_leds()  # ✅ Reset all LEDs before processing

    qr_data = None
    for barcode in decode(frame):
        qr_data = barcode.data.decode('utf-8')
        print(f"Scanned QR Code Data: {qr_data}")

        # ✅ Check user in database
        person_name, person_number, age, denied_by_police = check_person_in_database(qr_data)

        # ✅ Ensure `person_number` is not None
        if person_number is None:
            person_number = "Unknown"

        # ✅ Assign Access Level Based on Age or Politi-Merkning
        if person_name:
            if denied_by_police:
                logstatus = "Permission Denied - Politi-Merkning"
                GPIO.output(RED_LED, GPIO.HIGH)  # 🔴 Red LED ON
                status_message = "Bortvist av politiet - Access Denied"
                color = (255, 0, 0)
            elif age < 18:
                logstatus = "Permission Denied - Underage"
                GPIO.output(RED_LED, GPIO.HIGH)  # 🔴 Red LED ON
                status_message = "Under -18 - Ingen tilgang"
                color = (255, 0, 0)
            elif 18 <= age < 20:
                logstatus = "Limited Permission - No Spirits"
                GPIO.output(ORANGE_LED, GPIO.HIGH)  # 🟠 Orange LED ON
                status_message = "18+ Tillatt - Ingen sprit Over 22%"
                color = (255, 165, 0)
            else:
                logstatus = "Permission Granted - All Allowed"
                GPIO.output(GREEN_LED, GPIO.HIGH)  # 🟢 Green LED ON
                status_message = "Full tilgang - Alkohol & Tobakk tillatt"
                color = (0, 255, 0)
        else:
            logstatus = "Permission Denied - Not Registered"
            GPIO.output(RED_LED, GPIO.HIGH)  # 🔴 Red LED ON
            status_message = "Ikke registrert - Ingen tilgang"
            color = (255, 0, 0)

        # ✅ Prevent duplicate logs within 1 minute
        current_time = time.time()
        if person_name and (person_name not in last_logged_time or (current_time - last_logged_time[person_name]) > 60):
            insert_log(person_name, person_number, logstatus)  # ✅ Insert Log
            last_logged_time[person_name] = current_time

        return person_name, age, color, status_message  # ✅ Return details for display

    GPIO.output(BLUE_LED, GPIO.HIGH)  # ✅ No QR code detected → Turn ON BLUE LED
    logstatus = "No QR Code Detected"
    
    return None, None, (0, 0, 255), "No QR code detected"  # Default message

# ✅ Function to draw results on screen
def draw_results(frame, person_name, age, color, status_message):
    pil_image = Image.fromarray(cv2.cvtColor(frame, cv2.COLOR_BGR2RGB))
    draw = ImageDraw.Draw(pil_image)

    # ✅ Load font
    font_path = "/usr/share/fonts/truetype/dejavu/DejaVuSans-Bold.ttf"
    status_font = ImageFont.truetype(font_path, 30)
    name_font = ImageFont.truetype(font_path, 40)

    # ✅ Display name & age separately
    if person_name:
        text = f"{person_name} ({age} år)"
        draw.text((50, 100), text, font=name_font, fill=(255, 255, 255))

    # ✅ Display status message
    y0, dy = 200, 50
    for i, line in enumerate(status_message.split("\n")):
        y = y0 + i * dy
        draw.text((50, y), line, font=status_font, fill=color)

    return cv2.cvtColor(np.array(pil_image), cv2.COLOR_RGB2BGR)

# ✅ MAIN LOOP
try:
    while True:
        frame = camera.capture_array()
        person_name, age, color, status_message = process_qr_frame(frame)
        display_frame = draw_results(frame, person_name, age, color, status_message)
        cv2.imshow('QR Code Scanner', display_frame)

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
