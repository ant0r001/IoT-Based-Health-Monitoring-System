#define BLYNK_TEMPLATE_ID "TMPL62DQC_GBq"
#define BLYNK_TEMPLATE_NAME "Health Monitor System"
#define BLYNK_AUTH_TOKEN "YjnxOnpag0dkmfBXR0dDVTwosM5f0wzd"

#include <OneWire.h>
#include <DallasTemperature.h>
#include <Wire.h>
#include <DHT.h>
#include <BlynkSimpleEsp8266.h>
#include <U8g2lib.h>
#include <ESP8266HTTPClient.h>

// Replace with your WiFi credentials
char ssid[] = "Health";
char pass[] = "123456789";

// Replace with your Blynk authentication token
char auth[] = "YjnxOnpag0dkmfBXR0dDVTwosM5f0wzd";

// Replace with your server details
const char* server = "192.168.255.191";
const int serverPort = 80;
const String scriptPath = "http://192.168.255.191/health/health.php";

#include <Wire.h>
#include "MAX30105.h"

#include "heartRate.h"

MAX30105 particleSensor;

const byte RATE_SIZE = 4; //Increase this for more averaging. 4 is good.
byte rates[RATE_SIZE]; //Array of heart rates
byte rateSpot = 0;
long lastBeat = 0; //Time at which the last beat occurred

float beatsPerMinute;
int beatAvg;

long irValue= 0;




// Temperature sensor
#define ONE_WIRE_BUS 12
OneWire oneWire(ONE_WIRE_BUS);
DallasTemperature sensors(&oneWire);

// ECG sensor
#define ECG_OUTPUT_PIN A0
#define ECG_LO_MINUS_PIN D7
#define ECG_LO_PLUS_PIN D8

// DHT sensor
#define DHTPIN D3
#define DHTTYPE DHT11
DHT dht(DHTPIN, DHTTYPE);

// DS18B20 sensor
#define DS18B20_PIN 12
DeviceAddress ds18b20Address;
float ds18b20Temperature;

// Display
U8G2_SSD1306_128X64_NONAME_F_SW_I2C u8g2(U8G2_R0, /* clock=/D4, / data=/D5, / reset=*/U8X8_PIN_NONE);

// Reporting settings
#define REPORTING_PERIOD_MS 10000  // Adjust reporting period as needed
float temperature, humidity, ecgValue;
uint32_t tsLastReport = 0;

// Create an instance of the WiFiClient class
WiFiClient client;

void sendToMySQL() {
  if (client.connect(server, serverPort)) {
    Serial.println("Connected to server");

    String url = scriptPath + "?humidity=" + String(humidity) +
                 "&temperature=" + String(temperature) +
                 "&ecgValue=" + String(ecgValue) +
                 "&ds18b20Temperature=" + String(ds18b20Temperature) +
                 "&irValue=" + String( particleSensor.getIR()) +
                 "&bpm=" + String(( particleSensor.getIR()/1000)-25);

    Serial.println("Sending data to: " + url);

    client.print(String("GET ") + url + " HTTP/1.1\r\n" +
                 "Host: " + server + "\r\n" +
                 "Connection: close\r\n\r\n");

    Serial.println("Data sent");
  } else {
    Serial.println("Connection to server failed");
  }

  client.stop();
}


void setup() {
  Serial.begin(115200);
   Blynk.begin(BLYNK_AUTH_TOKEN, ssid, pass);

  // Connect to WiFi
  WiFi.begin(ssid, pass);
  while (WiFi.status() != WL_CONNECTED) {
    delay(1000);
    Serial.println("Connecting to WiFi...");
  }
  Serial.println("Connected to WiFi");

  // Display initialization
  u8g2.begin();
  u8g2.clearBuffer();
  u8g2.setFont(u8g2_font_5x8_tf);
  u8g2.setCursor(5, 10);
  u8g2.print("Welcome Health Monitor");
  u8g2.setCursor(5, 20);
  u8g2.print("Initializing sensors..");
  u8g2.sendBuffer();

  // DallasTemperature sensor initialization
  sensors.begin();

  // DHT sensor initialization
  dht.begin();

  // DS18B20 sensor initialization
  sensors.getAddress(ds18b20Address, 0);
  sensors.setResolution(ds18b20Address, 12);

  // ECG sensor initialization
  pinMode(ECG_OUTPUT_PIN, INPUT);
  pinMode(ECG_LO_MINUS_PIN, OUTPUT);
  pinMode(ECG_LO_PLUS_PIN, OUTPUT);

  Serial.println("Initializing sensors..");

  // Display success message
  u8g2.clearBuffer();
   u8g2.setCursor(5, 30);
  u8g2.print("Wifi Connected ");
  u8g2.sendBuffer();
  Serial.println("SUCCESS");


Serial.begin(115200);
  Serial.println("Initializing...");

  // Initialize sensor
  if (!particleSensor.begin(Wire, I2C_SPEED_FAST)) //Use default I2C port, 400kHz speed
  {
    Serial.println("MAX30102 was not found. Please check wiring/power. ");
    while (1);
  }
  Serial.println("Place your index finger on the sensor with steady pressure.");

  particleSensor.setup(); //Configure sensor with default settings
  particleSensor.setPulseAmplitudeRed(0x0A); //Turn Red LED to low to indicate sensor is running
  particleSensor.setPulseAmplitudeGreen(0);



}

void loop() {
  Blynk.run();


  long irValue = particleSensor.getIR();

  if (checkForBeat(irValue) == true)
  {
    //We sensed a beat!
    long delta = millis() - lastBeat;
    lastBeat = millis();

    beatsPerMinute = 60 / (delta / 1000.0);

    if (beatsPerMinute < 255 && beatsPerMinute > 20)
    {
      rates[rateSpot++] = (byte)beatsPerMinute; //Store this reading in the array
      rateSpot %= RATE_SIZE; //Wrap variable

      //Take average of readings
      beatAvg = 0;
      for (byte x = 0 ; x < RATE_SIZE ; x++)
        beatAvg += rates[x];
      beatAvg /= RATE_SIZE;
    }
  }

  Serial.print("IR=");
  Serial.print(irValue);
  Serial.print(", BPM=");
  Serial.print(beatsPerMinute);
  Serial.print(", Avg BPM=");
  Serial.print(beatAvg);

  if (irValue < 50000)
    Serial.print(" No finger?");

  Serial.println();

  // Read DHT sensor data
  humidity = dht.readHumidity();
  temperature = dht.readTemperature();

  // Read DS18B20 sensor data
  sensors.requestTemperatures();
  ds18b20Temperature = sensors.getTempCByIndex(0);

  // Read ECG sensor data
  digitalWrite(ECG_LO_MINUS_PIN, LOW);
  digitalWrite(ECG_LO_PLUS_PIN, HIGH);
  ecgValue = analogRead(ECG_OUTPUT_PIN);

  // Check for sensor reading failures
  if (isnan(humidity) || isnan(temperature) || ds18b20Temperature == DEVICE_DISCONNECTED_C) {
    Serial.println("Failed to read from sensors!");
  } else {
    // Send sensor data to Blynk app
    Blynk.virtualWrite(V1, humidity);
    Blynk.virtualWrite(V2, temperature);
    Blynk.virtualWrite(V3, ds18b20Temperature);
    Blynk.virtualWrite(V4, ecgValue);
    Blynk.virtualWrite(V7, (irValue/1000)-25);
     // Assuming V4 is used for ECG in your Blynk app

    // Send data to MySQL
    sendToMySQL();
  }

  // Report sensor data at regular intervals
  if (millis() - tsLastReport > REPORTING_PERIOD_MS) {
    Serial.print("Humidity: ");
    Serial.print(humidity);
    Serial.println(" %");

    // Set default values if BPM and SpO2 data are not available
    Blynk.virtualWrite(V6, 0);
    Blynk.virtualWrite(V7, 0);

    // Update display with sensor data
    // Update display with sensor data
    u8g2.clearBuffer();
    u8g2.setCursor(5, 10);
    u8g2.print("Humidity: ");
    u8g2.setCursor(90, 10);
    u8g2.print(humidity);
    u8g2.print(" %");

    u8g2.setCursor(5, 25);
    u8g2.print("Temperature: ");
    u8g2.setCursor(90, 25);
    u8g2.print(temperature);
    u8g2.setCursor(100, 25);
    u8g2.print("°C");

    u8g2.setCursor(5, 35);
    u8g2.print("DS18B20: ");
    u8g2.setCursor(90, 35);
    u8g2.print(ds18b20Temperature);
    u8g2.setCursor(100, 35);
    u8g2.print("°C");

    u8g2.setCursor(5, 45);
    u8g2.print("ECG Value: ");
    u8g2.setCursor(90, 45);
    u8g2.print(ecgValue);

    u8g2.setCursor(5, 55);
    u8g2.print("IR: ");
    u8g2.setCursor(90, 55);
    u8g2.print(irValue);

    u8g2.setCursor(5, 65);
    u8g2.print("BPM: ");
    u8g2.setCursor(90, 65);
    u8g2.print((irValue/1000)-10);

    u8g2.sendBuffer();

    // Send ECG value to Blynk every second
    Blynk.virtualWrite(V4, ecgValue);

    tsLastReport = millis();
  }
}