from flask import Flask, render_template, send_from_directory, json

app = Flask(__name__, template_folder="templates", static_folder="static")

ROUTE_FILE = "app.json"

def load_data():
    with open(ROUTE_FILE, "r") as file:
        return json.load(file)

@app.route("/")
def index():
    data = load_data()
    return render_template("app.html", route=data["predefinedRoute"])

@app.route("/app.json")
def get_json():
    return send_from_directory(".", "app.json")

if __name__ == "__main__":
    app.run(debug=True, port=5000)
