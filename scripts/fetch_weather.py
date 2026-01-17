import requests
import sys
import json
import pandas as pd
import os


script_dir = os.path.dirname(os.path.abspath(__file__))
data_dir = os.path.join(script_dir, '..', 'data')
os.makedirs(data_dir, exist_ok=True)  # create folder if it doesn't exist
csv_path = os.path.join(data_dir, 'weather_history.csv')


API_KEY = 'd402e52eb3117d6209fd8c47ab60e12a'  # Replace with your OpenWeather API key
BASE_URL = 'https://api.openweathermap.org/data/2.5/weather'


def get_weather(city):
    try:
        params = {'q': city, 'appid': API_KEY, 'units': 'metric'}
        response = requests.get(BASE_URL, params=params)
        data = response.json()

        if 'main' not in data:
            return {'error': 'City not found'}

        temp = data['main']['temp']
        humidity = data['main']['humidity']
        analysis = "Sunny day ahead!" if humidity < 60 else "Possible rain - check forecasts."

        # Add CSV history info
        if os.path.exists(csv_path):
            df = pd.read_csv(csv_path)
            avg_temp = df['temp'].mean() if not df.empty else temp
            analysis += f" Average temp this month: {avg_temp:.1f} C"
        else:
            analysis += " No historical data yet."

        # Save current data to CSV
        new_data = pd.DataFrame([[city, temp, humidity]], columns=['city', 'temp', 'humidity'])
        if os.path.exists(csv_path):
            new_data.to_csv(csv_path, mode='a', header=False, index=False)
        else:
            new_data.to_csv(csv_path, index=False)

        return {'temp': temp, 'humidity': humidity, 'analysis': analysis}

    except Exception as e:
        return {'error': str(e)}


if __name__ == "__main__":
    city = sys.argv[1] if len(sys.argv) > 1 else 'London'
    print(json.dumps(get_weather(city)))
