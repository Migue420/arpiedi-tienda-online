import trimesh
import json
import os
import argparse

def analyze_3d_model(file_path, model_name, material, style):
    """Analyzes a 3D file and returns a dictionary with technical and descriptive data."""

    # --- 1. Technical Analysis with Trimesh ---
    try:
        # Load the 3D model. Trimesh automatically handles OBJ, GLB, STL, etc.
        mesh = trimesh.load_mesh(file_path)
    except Exception as e:
        return {"error": f"Could not load file: {e}"}

    # Technical Data Collection
    technical_data = {
        "file_format": os.path.splitext(file_path)[1].lstrip('.').upper(),
        "num_vertices": len(mesh.vertices) if hasattr(mesh, 'vertices') else "N/A",
        "num_faces": len(mesh.faces) if hasattr(mesh, 'faces') else "N/A",
        "volume_cm3": round(mesh.volume * 1000, 2) if mesh.is_watertight else "N/A (Not sealed)",
        "surface_area_cm2": round(mesh.area * 100, 2), # Assuming units in meters for conversion to cm
        "file_size_mb": round(os.path.getsize(file_path) / (1024 * 1024), 2)
    }

    # --- 2. Descriptive Data (Human Input) ---
    descriptive_data = {
        "model_name": model_name,
        "main_material": material,
        "style_category": style,
        "quick_summary": f"{model_name}, made of {material} ({style})."
    }

    # --- 3. Combine and Return ---
    final_result = {
        "description": descriptive_data,
        "technical_data": technical_data
    }

    return final_result

def save_json(data, output_name):
    """Saves the data dictionary to a JSON file."""
    with open(f"{output_name}.json", 'w') as f:
        json.dump(data, f, indent=4)
    print(f"✅ JSON file saved as: {output_name}.json")

# --- Execution Logic (Example Usage) ---
if __name__ == "__main__":
    parser = argparse.ArgumentParser(description='Analyze a 3D model and generate a JSON description.')
    parser.add_argument('model_path', type=str, help='The path to the 3D model file (OBJ, GLB, STL).')
    parser.add_argument('--name', type=str, required=True, help='The name of the model.')
    parser.add_argument('--material', type=str, required=True, help='The primary material of the model.')
    parser.add_argument('--style', type=str, required=True, help='The style or category of the model.')
    parser.add_argument('--output', type=str, help='The name of the output JSON file (without extension).')

    args = parser.parse_args()

    print(f"🔎 Analyzing model: {args.model_path}")

    analyzed_data = analyze_3d_model(args.model_path, args.name, args.material, args.style)

    if "error" not in analyzed_data:
        output_filename = args.output if args.output else os.path.splitext(os.path.basename(args.model_path))[0] + "_sheet"
        save_json(analyzed_data, output_filename)
    else:
        print(f"❌ Error processing: {analyzed_data['error']}")
