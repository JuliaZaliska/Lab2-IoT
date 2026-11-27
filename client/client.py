import getpass
import re
import requests

BASE_URL = "http://127.0.0.1:8000"


def show_sensors(only_alerts=False):
    params = {"alerts": 1} if only_alerts else {}
    resp = requests.get(f"{BASE_URL}/api/sensors", params=params, timeout=5)
    resp.raise_for_status()
    data = resp.json()

    title = "АКТИВНІ ТРИВОГИ" if only_alerts else "УСІ ДАТЧИКИ"
    print(f"\n=== {title} (знайдено: {data['count']}) ===")
    print("-" * 75)
    for s in data["items"]:
        loc = s["location"]["name"] if s["location"] else "—"
        flag = "🚨 ALERT" if s["isAlert"] else s["status"].upper()
        print(f"[{s['id']}] {s['name']} ({loc}) | {s['currentValue']} {s['unit']} | {flag}")
    print("-" * 75)


def show_sensor_detail():
    sensor_id = input("Введіть ID датчика: ").strip()
    resp = requests.get(f"{BASE_URL}/api/sensors/{sensor_id}", timeout=5)

    if resp.status_code == 404:
        print("Датчик із таким ID не знайдено.")
        return

    resp.raise_for_status()
    s = resp.json()
    loc = s["location"] or {}

    print("\n" + "=" * 60)
    print(f"Датчик:    {s['name']} ({s['sensorType']})")
    print(f"Локація:   {loc.get('name', '—')} ({loc.get('zone') or '—'})")
    print(f"Показник:  {s['currentValue']} {s['unit']}")
    print(f"Поріг:     {s['alertThreshold']} {s['unit']}" if s["alertThreshold"] is not None else "Поріг:     —")
    print(f"Статус:    {'🚨 ТРИВОГА' if s['isAlert'] else s['status'].upper()}")
    print(f"Оновлено:  {s['updatedAt']}")
    print("=" * 60)


def login_to_admin():
    print("\n--- Вхід в адмінку через сесію ---")
    email = input("Email: ").strip()
    password = getpass.getpass("Пароль: ")

    session = requests.Session()
    login_page = session.get(f"{BASE_URL}/login", timeout=5)
    login_page.raise_for_status()

    match = re.search(r'name="_csrf_token"\s+value="([^"]+)"', login_page.text)
    csrf_token = match.group(1) if match else ""

    payload = {
        "_username": email,
        "_password": password,
        "_csrf_token": csrf_token,
    }

    session.post(f"{BASE_URL}/login", data=payload, allow_redirects=True, timeout=5)
    admin_resp = session.get(f"{BASE_URL}/admin", allow_redirects=True, timeout=5)

    if "/login" in admin_resp.url or admin_resp.status_code != 200:
        print("Помилка входу: невірний логін/пароль або немає прав ROLE_ADMIN.")
    else:
        print(f"Успішний вхід! Доступ до адмінки отримано: {admin_resp.url} (HTTP {admin_resp.status_code})")


def main():
    while True:
        print("\n=== IoT Telemetry Python Client ===")
        print("1. Усі датчики (JSON API)")
        print("2. Тільки активні тривоги (alerts=1)")
        print("3. Деталі датчика за ID")
        print("4. Перевірити вхід в адмінку (сесія)")
        print("0. Вихід")

        choice = input("Оберіть дію: ").strip()
        try:
            if choice == "1":
                show_sensors(only_alerts=False)
            elif choice == "2":
                show_sensors(only_alerts=True)
            elif choice == "3":
                show_sensor_detail()
            elif choice == "4":
                login_to_admin()
            elif choice == "0":
                break
            else:
                print("Невідома команда.")
        except requests.RequestException as e:
            print(f"Помилка з'єднання із сервером: {e}")


if __name__ == "__main__":
    main()