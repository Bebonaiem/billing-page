# BillingHub GitHub Setup Guide

This guide explains how to set up BillingHub on GitHub for distribution and collaboration.

## Step 1: Create GitHub Repository

### Option A: Create New Repository

1. Go to [github.com/new](https://github.com/new)
2. Repository name: `billinghub`
3. Description: `Complete open-source billing and invoicing system`
4. Select: **Public** (for distribution)
5. Initialize with README (optional - we have one)
6. Create repository

### Option B: Push Existing Code

```bash
git init
git add .
git commit -m "Initial commit: BillingHub v1.0.0"
git remote add origin https://github.com/yourusername/billinghub.git
git branch -M main
git push -u origin main
```

## Step 2: Repository Settings

### Add Description and Topics

1. Go to repository Settings
2. Add description: "Complete open-source billing and invoicing system"
3. Add topics:
   - `billing`
   - `invoicing`
   - `laravel`
   - `payment-gateway`
   - `open-source`
   - `saas`
   - `pterodactyl`

### Enable Features

In Settings → Code and automation:

- ✅ Issues
- ✅ Discussions
- ✅ Projects
- ✅ Wiki

## Step 3: Create Release

### Option A: Manual Release (via GitHub Web)

1. Go to Releases tab
2. Click "Create a new release"
3. Tag: `v1.0.0`
4. Release title: `BillingHub v1.0.0 - Initial Release`
5. Description: Use content from `releases/RELEASE_NOTES_v1.0.0.md`
6. Upload files:
   - `billinghub-1.0.0.zip`
   - `billinghub-1.0.0.sha256`
   - `billinghub-1.0.0.md5`
7. Publish release

### Option B: Automated Release (via Git Tag)

```bash
# Create and push tag
git tag -a v1.0.0 -m "Release v1.0.0"
git push origin v1.0.0

# GitHub Actions will automatically:
# - Package the release
# - Generate checksums
# - Create GitHub release
# - Upload files
```

## Step 4: Configure GitHub Actions

The repository includes `.github/workflows/release.yml` which:

1. Automatically triggers on tag push (`git push --tags`)
2. Packages the release
3. Generates checksums
4. Creates GitHub release
5. Uploads files

### Make Workflow Executable

```bash
chmod +x scripts/package-release.sh
git add scripts/package-release.sh
git commit -m "Make package script executable"
git push
```

## Step 5: Website Integration

### Update Website Links

Edit `website/index.html`:
- Change `yourusername` in GitHub link
- Update download button link
- Add correct domain name

Edit `website/download.html`:
- Update GitHub release download URL
- Verify version numbers

### Deploy Website

```bash
# Option 1: GitHub Pages
# Enable in Settings → Pages
# Select branch: main
# Folder: /website

# Option 2: Manual hosting
# Upload /website folder to your host
# Point domain.com to /website directory
```

## Step 6: Create Releases Directory

The release packaging script requires a `releases/` directory:

```bash
mkdir -p releases
git add .gitkeep -f releases/.gitkeep
git commit -m "Add releases directory"
git push
```

(Or add to `.gitignore` if you don't want to commit releases)

## Step 7: Update Installation Links

### In `installer/README.md`

Update references:
```markdown
- Support: https://github.com/yourusername/billinghub/issues
- Documentation: https://docs.billinghub.local
```

### In `website/download.html`

Update:
```html
<a href="https://github.com/yourusername/billinghub/releases/download/v1.0.0/billinghub-1.0.0.zip">
```

## Step 8: Create First Release

### Manual Steps

```bash
# 1. Ensure all changes are committed
git status

# 2. Update version numbers
# Edit composer.json, package.json version fields

# 3. Create release notes
# Update CHANGELOG.md

# 4. Commit version bump
git add .
git commit -m "Bump version to 1.0.0"
git push

# 5. Create tag
git tag -a v1.0.0 -m "Release v1.0.0 - Initial stable release"

# 6. Push tag (triggers GitHub Actions)
git push origin v1.0.0
```

### Monitor GitHub Actions

1. Go to Actions tab
2. Watch "Release Package" workflow
3. Check for successful completion
4. Verify files appear in Releases

## Step 9: Distribution

### GitHub Pages Website

```bash
# Create gh-pages branch
git checkout --orphan gh-pages

# Add website files
git add website/
git commit -m "Add GitHub Pages site"
git push origin gh-pages

# Enable in Settings → Pages
# Select branch: gh-pages
# Folder: /website
```

Site will be available at: `https://yourusername.github.io/billinghub/`

### README Links

Add to main README:

```markdown
## 📥 Download

- **Latest Release:** [GitHub Releases](https://github.com/yourusername/billinghub/releases)
- **Website:** [billinghub.yoursite.com](https://billinghub.yoursite.com)
- **Documentation:** [Online Docs](https://yourusername.github.io/billinghub/docs.html)
```

## Step 10: Ongoing Maintenance

### Updating Releases

1. Make changes to code
2. Commit and push
3. Update version numbers
4. Update CHANGELOG.md
5. Create new tag
6. Push tag (GitHub Actions handles rest)

### Creating Beta/RC Releases

```bash
# For release candidates
git tag -a v1.1.0-rc.1 -m "Release candidate 1"
git push origin v1.1.0-rc.1

# Mark as pre-release in GitHub UI
```

### Deleting Release

```bash
# Delete local tag
git tag -d v1.0.0

# Delete remote tag
git push origin --delete v1.0.0

# Delete GitHub release via web UI
```

## Checklist

- [ ] Repository created on GitHub
- [ ] Description and topics added
- [ ] Issues and Discussions enabled
- [ ] GitHub Actions configured
- [ ] `.gitignore` configured properly
- [ ] README.md complete
- [ ] LICENSE file added
- [ ] First release created and tagged
- [ ] Website deployed
- [ ] Download links working
- [ ] Documentation accessible
- [ ] GitHub Pages enabled (optional)

## Troubleshooting

### Release Workflow Fails

1. Check Actions tab for error logs
2. Verify `scripts/package-release.sh` is executable
3. Ensure zip command is available in runner
4. Check for missing environment variables

### Files Not Uploading

1. Verify file paths in workflow
2. Check GitHub token permissions
3. Ensure files exist before release step

### Release Not Showing

1. Refresh page (wait ~30 seconds)
2. Check if marked as draft
3. Verify tag format (v1.0.0)

## Example Release

For v1.0.0:

```bash
# Commit changes
git add .
git commit -m "v1.0.0: Complete billing system"

# Tag release
git tag -a v1.0.0 -m "BillingHub v1.0.0 - Initial Release"

# Push (both commit and tag)
git push origin main
git push origin v1.0.0

# GitHub Actions automatically:
# 1. Creates package
# 2. Generates checksums
# 3. Creates release page
# 4. Uploads files
```

## Support

For help with GitHub:
- [GitHub Docs](https://docs.github.com)
- [GitHub Actions](https://docs.github.com/en/actions)
- [Releases Help](https://docs.github.com/en/repositories/releasing-projects-on-github)

---

**Next:** Promote your release on social media, forums, and package repositories!
