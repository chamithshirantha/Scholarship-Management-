<?php

namespace App\Http\Controllers;

use App\Models\Application;
use App\Models\Document;
use Exception;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;

class DocumentController extends Controller
{
    public function store(Request $request, $applicationId): JsonResponse
    {
        try {
            // Validate the request
            $request->validate([
                'document_type' => 'required|string|max:255',
                'document_file' => 'required|file|mimes:jpeg,png,jpg,pdf,doc,docx|max:5120', // 5MB max
            ]);

            // Check if application exists and user owns it
            $application = Application::findOrFail($applicationId);
            
            if ($application->user_id !== auth()->id()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Unauthorized to upload documents for this application'
                ], 403);
            }

            // Check if application is still editable
            if (!in_array($application->status, ['pending', 'under_review'])) {
                return response()->json([
                    'success' => false,
                    'message' => 'Cannot upload documents for applications that are already processed'
                ], 422);
            }

            // Store the file
            $file = $request->file('document_file');
            $fileName = time() . '_' . $file->getClientOriginalName();
            $filePath = $file->storeAs('documents/' . $applicationId, $fileName, 'public');

            // Create document record
            $document = Document::create([
                'application_id' => $applicationId,
                'document_type' => $request->document_type,
                'file_path' => $filePath,
                'file_name' => $file->getClientOriginalName(),
                'mime_type' => $file->getMimeType(),
                'file_size' => $file->getSize(),
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Document uploaded successfully',
                'data' => $document
            ], 201);

        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Application not found'
            ], 404);

        } catch (\Illuminate\Validation\ValidationException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Validation failed',
                'errors' => $e->errors()
            ], 422);

        } catch (Exception $e) {
            Log::error('Error uploading document: ' . $e->getMessage());

            return response()->json([
                'success' => false,
                'message' => 'Failed to upload document',
                'error' => 'Server error'
            ], 500);
        }
    }

    public function show($documentId): JsonResponse
    {
        try {
            $document = Document::with('application')->findOrFail($documentId);

            // Check if user owns this document
            if ($document->application->user_id !== auth()->id()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Unauthorized to view this document'
                ], 403);
            }

            return response()->json([
                'success' => true,
                'message' => 'Document retrieved successfully',
                'data' => $document
            ]);

        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Document not found'
            ], 404);

        } catch (Exception $e) {
            Log::error('Error retrieving document: ' . $e->getMessage());

            return response()->json([
                'success' => false,
                'message' => 'Failed to retrieve document',
                'error' => 'Server error'
            ], 500);
        }
    }

    public function download($documentId): \Symfony\Component\HttpFoundation\BinaryFileResponse|JsonResponse
    {
        try {
            $document = Document::with('application')->findOrFail($documentId);

            // Check if user owns this document or is admin
            if ($document->application->user_id !== auth()->id() && !auth()->user()->isAdmin()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Unauthorized to download this document'
                ], 403);
            }

            // Check if file exists
            if (!Storage::disk('public')->exists($document->file_path)) {
                return response()->json([
                    'success' => false,
                    'message' => 'File not found'
                ], 404);
            }

            return response()->download(
                storage_path('app/public/' . $document->file_path),
                $document->file_name
            );

        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Document not found'
            ], 404);

        } catch (Exception $e) {
            Log::error('Error downloading document: ' . $e->getMessage());

            return response()->json([
                'success' => false,
                'message' => 'Failed to download document',
                'error' => 'Server error'
            ], 500);
        }
    }

    public function destroy($documentId): JsonResponse
    {
        try {
            $document = Document::with('application')->findOrFail($documentId);

            // Check if user owns this document
            if ($document->application->user_id !== auth()->id()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Unauthorized to delete this document'
                ], 403);
            }

            // Check if application is still editable
            if (!in_array($document->application->status, ['pending', 'under_review'])) {
                return response()->json([
                    'success' => false,
                    'message' => 'Cannot delete documents for applications that are already processed'
                ], 422);
            }

            // Delete the file from storage
            if (Storage::disk('public')->exists($document->file_path)) {
                Storage::disk('public')->delete($document->file_path);
            }

            // Delete the database record
            $document->delete();

            // Create application log
            // $this->createApplicationLog($document->application_id, 'document_delete', 'Document deleted: ' . $document->document_type);

            return response()->json([
                'success' => true,
                'message' => 'Document deleted successfully'
            ]);

        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Document not found'
            ], 404);

        } catch (Exception $e) {
            Log::error('Error deleting document: ' . $e->getMessage());

            return response()->json([
                'success' => false,
                'message' => 'Failed to delete document',
                'error' => 'Server error'
            ], 500);
        }
    }

    public function getApplicationDocuments($applicationId): JsonResponse
    {
        try {
            $application = Application::findOrFail($applicationId);

            // Check if user owns this application or is admin
            if ($application->user_id !== auth()->id() && !auth()->user()->isAdmin()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Unauthorized to view these documents'
                ], 403);
            }

            $documents = Document::where('application_id', $applicationId)->get();

            return response()->json([
                'success' => true,
                'message' => 'Documents retrieved successfully',
                'data' => $documents
            ]);

        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Application not found'
            ], 404);

        } catch (Exception $e) {
            Log::error('Error retrieving application documents: ' . $e->getMessage());

            return response()->json([
                'success' => false,
                'message' => 'Failed to retrieve documents',
                'error' => 'Server error'
            ], 500);
        }
    }
}
